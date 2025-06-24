<?php

namespace App\Http\Controllers;

use App\Models\Prediction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class PredictionController extends Controller
{
    public function index()
    {
        $predictions = Prediction::latest()->paginate(10);
        return view('predictions.index', compact('predictions'));
    }

    public function create()
    {
        return view('predictions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:50048'
        ], [
            'photo.required' => 'Foto harus diupload',
            'photo.image' => 'File harus berupa gambar',
            'photo.mimes' => 'Format gambar harus jpeg, png, atau jpg',
            'photo.max' => 'Ukuran gambar maksimal 50MB'
        ]);

        try {
            $apiUrl = env('MODEL_API');

            if (!$apiUrl) {
                return back()->with('error', 'API URL tidak dikonfigurasi.');
            }

            // Simpan file ke storage
            $file = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $storedPath = $file->storeAs('photos', $filename, 'public');
            
            // Kirim ke API untuk prediksi
            $response = Http::attach(
                'file', 
                file_get_contents($file->getRealPath()), 
                $filename
            )->post($apiUrl);

            if ($response->successful()) {
                $apiData = $response->json();
                
                // Proses hasil prediksi
                $processedResult = $this->processApiResponse($apiData);

                // Jika predicted_class = 0, anggap sebagai gagal upload
                if ($processedResult['predicted_class'] == 3) {
                    // Foto tidak sesuai
                    Storage::disk('public')->delete($storedPath);
                    return back()->with('error', 'Gagal upload foto. Foto tidak sesuai untuk deteksi air.');
                }
                

                // Simpan hasil ke database hanya jika berhasil
                $prediction = Prediction::create([
                    'image_path' => $storedPath,
                    'predicted_class' => $processedResult['predicted_class'],
                    'water_quality' => $processedResult['water_quality']
                ]);

                return redirect()->route('predictions.show', $prediction->id)
                    ->with('success', 'Deteksi berhasil dilakukan!');
            } else {
                // Hapus file jika API gagal
                Storage::disk('public')->delete($storedPath);
                return back()->with('error', 'Gagal melakukan prediksi. Silakan coba lagi.');
            }

        } catch (\Exception $e) {
            // Hapus file jika terjadi error
            if (isset($storedPath)) {
                Storage::disk('public')->delete($storedPath);
            }
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(Prediction $prediction)
    {
        return view('predictions.show', compact('prediction'));
    }

    public function destroy(Prediction $prediction)
    {
        // Hapus file gambar
        Storage::disk('public')->delete($prediction->image_path);
        
        // Hapus record dari database
        $prediction->delete();

        return redirect()->route('predictions.index')
            ->with('success', 'Data prediksi berhasil dihapus!');
    }

    private function processApiResponse($apiData)
{
    $predictedClass = $apiData['predicted_class'];
    
    // Tentukan kualitas air berdasarkan predicted_class
    switch ($predictedClass) {
        case 0:
            $waterQuality = 'Bersih';
            break;
        case 1:
            $waterQuality = 'Keruh';
            break;
        case 2:
            $waterQuality = 'Kotor';
            break;
        case 3:
        default:
            $waterQuality = 'Foto Tidak Sesuai';
            break;
    }

    return [
        'predicted_class' => $predictedClass,
        'water_quality' => $waterQuality
    ];
}
}