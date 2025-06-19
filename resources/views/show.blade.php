@extends('layouts.app')

@section('title', 'Detail Hasil Deteksi')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Detail Hasil Deteksi</h2>
            <a href="{{ route('predictions.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Kembali
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Image Display -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-image me-2"></i>
                    Gambar Sample
                </h5>
            </div>
            <div class="card-body text-center">
                <img src="{{ $prediction->image_url }}" 
                     alt="Sample Image" 
                     class="img-fluid rounded shadow"
                     style="max-height: 400px;">
                <div class="mt-3">
                    <small class="text-muted">
                        File: {{ $prediction->filename }}<br>
                        Upload: {{ $prediction->formatted_created_at }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Display -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header bg-{{ $prediction->class_color }} text-white">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Hasil Analisis
                </h5>
            </div>
            <div class="card-body">
                <!-- Main Result -->
                <div class="alert alert-{{ $prediction->class_color }} text-center mb-4">
                    <h3 class="alert-heading mb-2">
                        <i class="fas fa-{{ $prediction->prediction_class == 'Bersih' ? 'check-circle' : ($prediction->prediction_class == 'Sedang' ? 'exclamation-circle' : 'times-circle') }} me-2"></i>
                        {{ $prediction->prediction_class ?? 'Unknown' }}
                    </h3>
                    <p class="mb-0">
                        <strong>Confidence: {{ number_format($prediction->confidence, 2) }}%</strong><br>
                        <small>Tingkat Kepercayaan: {{ $prediction->confidence_level }}</small>
                    </p>
                </div>

                <!-- Confidence Progress -->
                <div class="mb-4">
                    <label class="form-label">Overall Confidence</label>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-{{ $prediction->confidence > 80 ? 'success' : ($prediction->confidence > 60 ? 'warning' : 'danger') }}" 
                             style="width: {{ $prediction->confidence }}%">
                            {{ number_format($prediction->confidence, 1) }}%
                        </div>
                    </div>
                </div>

                <!-- Class Probabilities -->
                @if($prediction->probabilities_with_labels)
                    <div class="mb-4">
                        <h6>Probabilitas per Kelas:</h6>
                        @foreach($prediction->probabilities_with_labels as $prob)
                            <div class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <span>{{ $prob['label'] }}</span>
                                    <span>{{ $prob['percentage'] }}%</span>
                                </div>
                                <div class="progress" style="height: 15px;">
                                    <div class="progress-bar bg-{{ $prob['label'] == 'Bersih' ? 'success' : ($prob['label'] == 'Sedang' ? 'warning' : 'danger') }}" 
                                         style="width: {{ $prob['percentage'] }}%">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Raw API Response -->
                <div class="mb-3">
                    <h6>Raw API Response:</h6>
                    <div class="bg-light p-3 rounded">
                        <pre class="mb-0" style="font-size: 12px;">{{ json_encode($prediction->result, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="card shadow mt-3">
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ $prediction->image_url }}" 
                       target="_blank" 
                       class="btn btn-outline-primary">
                        <i class="fas fa-download me-2"></i>
                        Download Gambar
                    </a>
                    <form action="{{ route('predictions.destroy', $prediction) }}" 
                          method="POST" 
                          onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="fas fa-trash me-2"></i>
                            Hapus Data
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection