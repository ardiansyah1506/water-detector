@extends('layouts.app')

@section('title', 'Deteksi Kualitas Air - Dashboard')

@section('content')
<div class="row">
    <!-- Upload Form -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-upload me-2"></i>
                    Upload Gambar Air
                </h5>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('predictions.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="upload-area mb-3">
                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Pilih gambar air untuk dianalisis</p>
                        <input type="file" 
                               name="photo" 
                               id="fileInput" 
                               class="form-control" 
                               accept="image/*" 
                               onchange="previewImage(this)" 
                               required>
                        <small class="text-muted">Format: JPG, PNG, JPEG (Max: 2MB)</small>
                    </div>

                    <div id="preview-container" style="display: none;" class="mb-3 text-center">
                        <img id="preview" src="#" alt="Preview" class="preview-image">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>
                        Analisis Gambar
                    </button>
                </form>
            </div>
        </div>

        <!-- Latest Result Card -->
        @if(session('prediction_data'))
            <div class="card shadow mt-3">
                <div class="card-header bg-{{ session('prediction_data')['class'] == 'Bersih' ? 'success' : (session('prediction_data')['class'] == 'Sedang' ? 'warning' : 'danger') }} text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Hasil Deteksi Terbaru
                    </h6>
                </div>
                <div class="card-body">
                    @php $data = session('prediction_data') @endphp
                    <div class="text-center mb-3">
                        <h4 class="text-{{ $data['class'] == 'Bersih' ? 'success' : ($data['class'] == 'Sedang' ? 'warning' : 'danger') }}">
                            {{ $data['class'] }}
                        </h4>
                        <p class="mb-0">Confidence: {{ $data['raw_confidence'] }}</p>
                    </div>
                    
                    <div class="progress mb-2" style="height: 20px;">
                        <div class="progress-bar bg-{{ $data['confidence'] > 80 ? 'success' : ($data['confidence'] > 60 ? 'warning' : 'danger') }}" 
                             style="width: {{ $data['confidence'] }}%">
                            {{ number_format($data['confidence'], 1) }}%
                        </div>
                    </div>
                    
                    <small class="text-muted">
                        Tingkat Kepercayaan: 
                        @if($data['confidence'] >= 90) Sangat Tinggi
                        @elseif($data['confidence'] >= 80) Tinggi
                        @elseif($data['confidence'] >= 70) Sedang
                        @elseif($data['confidence'] >= 60) Rendah
                        @else Sangat Rendah
                        @endif
                    </small>
                </div>
            </div>
        @endif
    </div>

    <!-- Results Table -->
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Riwayat Deteksi
                </h5>
                <span class="badge bg-light text-dark">{{ $predictions->total() }} Total</span>
            </div>
            <div class="card-body p-0">
                @if($predictions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Gambar</th>
                                    <th>Hasil Deteksi</th>
                                    <th>Confidence</th>
                                    <th>Tingkat</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($predictions as $prediction)
                                <tr>
                                    <td>
                                        <img src="{{ $prediction->image_url }}" 
                                             alt="Sample" 
                                             class="rounded" 
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $prediction->class_color }}">
                                            {{ $prediction->prediction_class ?? 'Unknown' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($prediction->confidence)
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-{{ $prediction->confidence > 80 ? 'success' : ($prediction->confidence > 60 ? 'warning' : 'danger') }}" 
                                                     style="width: {{ $prediction->confidence }}%">
                                                    {{ number_format($prediction->confidence, 1) }}%
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $prediction->confidence_level }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $prediction->formatted_created_at }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('predictions.show', $prediction) }}" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('predictions.destroy', $prediction) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="card-footer">
                        {{ $predictions->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada data deteksi</h5>
                        <p class="text-muted">Upload gambar pertama Anda untuk memulai analisis</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection