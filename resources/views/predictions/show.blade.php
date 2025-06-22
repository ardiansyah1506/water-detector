@extends('layouts.app')

@section('title', 'Hasil Deteksi')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">
                    <i class="fas fa-check-circle me-2"></i>
                    Hasil Deteksi Kualitas Air
                </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-3">Gambar yang Dianalisis:</h5>
                        <div class="text-center">
                            <img src="{{ $prediction->image_url }}" alt="Foto Air" class="img-fluid rounded shadow" style="max-height: 400px;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5 class="mb-3">Hasil Analisis:</h5>
                        
                        <div class="card bg-light mb-3 bg-success">
                            <div class="card-body text-center">
                                <div class="mb-4">
                                    <i class="{{ $prediction->status_icon }} fa-4x {{ $prediction->predicted_class == 2 ? 'text-success' : 'text-danger' }}"></i>
                                </div>
                                <h2 class="mb-3">
                                    <span class="badge {{ $prediction->predicted_class == 2 ? 'bg-success' : 'bg-danger' }} fs-4">
                                        {{ $prediction->water_quality_label }}
                                    </span>
                                </h2>
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="text-muted mb-1">Kelas Prediksi</h6>
                                        <h4 class="text-info mb-0">{{ $prediction->predicted_class }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert {{ $prediction->predicted_class == 2 ? 'alert-success' : 'alert-danger' }}">
                            <h6 class="alert-heading">
                                <i class="{{ $prediction->status_icon }} me-2"></i>
                                Interpretasi Hasil:
                            </h6>
                            @if($prediction->predicted_class == 1)
                            <p class="mb-0">Air terdeteksi dalam kondisi <strong>kotor</strong>. Disarankan untuk melakukan treatment sebelum digunakan.</p>
                            @else
                            <p class="mb-0">Air terdeteksi dalam kondisi <strong>bersih</strong>. Air ini kemungkinan aman untuk digunakan.</p>
                            @endif
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Informasi Tambahan:</h6>
                                <ul class="list-unstyled mb-0">
                                    <li><i class="fas fa-calendar me-2 text-muted"></i>Tanggal: {{ $prediction->created_at->format('d/m/Y H:i') }}</li>
                                    <li><i class="fas fa-hashtag me-2 text-muted"></i>ID Prediksi: #{{ $prediction->id }}</li>
                                    <li><i class="fas fa-tag me-2 text-muted"></i>Status: {{ $prediction->water_quality }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex gap-2 justify-content-center">
                    <a href="{{ route('predictions.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Deteksi Baru
                    </a>
                    <a href="{{ route('predictions.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-list me-2"></i>
                        Lihat Riwayat
                    </a>
                    <form action="{{ route('predictions.destroy', $prediction) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-trash me-2"></i>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection