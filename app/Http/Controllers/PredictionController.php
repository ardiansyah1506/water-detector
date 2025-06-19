<?php

namespace App\Http\Controllers;

use App\Models\Prediction;
use Http;
use Illuminate\Http\Request;
use Storage;

class PredictionController extends Controller
{
        private $classLabels = [
            0 => 'Bersih',
            1 => 'Sedang', 
            2 => 'Keruh'
        ];
    
        public function index()
        {
            $predictions = Prediction::latest()->paginate(10);
            return view('index', compact('predictions'));
        }
    
        public function store(Request $request)
        {
            $request->validate([
                'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
            ], [
                'photo.required' => 'Foto harus diupload',
                'photo.image' => 'File harus berupa gambar',
                'photo.mimes' => 'Format gambar harus jpeg, png, atau jpg',
                'photo.max' => 'Ukuran gambar maksimal 2MB'
            ]);
    
            try {
                $apiUrl = env('MODEL_API');

                if (!$apiUrl) {
                    abort(500, 'API URL tidak dikonfigurasi.');
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
                    dd($response);
                    // Proses hasil prediksi
                    $processedResult = $this->processApiResponse($apiData);

                    // Simpan hasil ke database
                    $prediction = Prediction::create([
                        'image_path' => $storedPath,
                        'filename' => $filename,
                        'result' => json_encode($apiData), // Raw API response
                        'status' => 'success',
                        'confidence' => $processedResult['confidence'],
                        'prediction_class' => $processedResult['class'],
                        'prediction_index' => $processedResult['index'],
                        'class_probabilities' => json_encode($processedResult['probabilities']),
                    ]);
    
                    return redirect()->route('predictions.index')
                        ->with('success', 'Deteksi berhasil dilakukan!')
                        ->with('prediction_data', $processedResult)
                        ->with('prediction_id', $prediction->id);
                } else {
                    return back()->with('error', 'Gagal melakukan prediksi. Silakan coba lagi.');
                }
    
            } catch (\Exception $e) {
                return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        }
    
        private function processApiResponse($apiData)
        {
            $predictedClass = $apiData['predicted_class'][0] ?? [];
            $confidenceScore = $apiData['confidence_score'] ?? '0%';
            
            // Cari index dengan nilai probabilitas tertinggi
            $maxProbability = max($predictedClass);
            $predictedIndex = array_search($maxProbability, $predictedClass);
            
            // Konversi confidence score dari string ke float
            $confidence = (float) str_replace('%', '', $confidenceScore);
            
            return [
                'class' => $this->classLabels[$predictedIndex] ?? 'Unknown',
                'index' => $predictedIndex,
                'confidence' => $confidence,
                'probabilities' => $predictedClass,
                'raw_confidence' => $confidenceScore
            ];
        }
    
        public function show(Prediction $prediction)
        {
            return view('show', compact('prediction'));
        }
    
        public function destroy(Prediction $prediction)
        {
            // Hapus file dari storage
            if (Storage::disk('public')->exists($prediction->image_path)) {
                Storage::disk('public')->delete($prediction->image_path);
            }
    
            $prediction->delete();
    
            return redirect()->route('predictions.index')
                ->with('success', 'Data prediksi berhasil dihapus');
        }
    
}

