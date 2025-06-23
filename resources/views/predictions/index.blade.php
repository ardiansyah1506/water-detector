@extends('layouts.app')

@section('title', 'Riwayat Deteksi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">
            <i class="fas fa-history me-2"></i>
            Riwayat Deteksi Kualitas Air
        </h2>
        <p class="text-muted mb-0">Total: {{ $predictions->total() }} deteksi</p>
    </div>
    <a href="{{ route('predictions.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>
        Deteksi Baru
    </a>
</div>

<!-- Statistik Ringkas -->
@if($predictions->total() > 0)
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                    <h4>{{ $predictions->where('predicted_class', 0)->count() }}</h4>
                    <small>Air Bersih</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fas fa-water fa-2x mb-2"></i>
                    <h4>{{ $predictions->where('predicted_class', 1)->count() }}</h4>
                    <small>Air Keruh</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <i class="fas fa-times-circle fa-2x mb-2"></i>
                    <h4>{{ $predictions->where('predicted_class', 2)->count() }}</h4>
                    <small>Air Kotor</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <h4>{{ $predictions->where('predicted_class', 3)->count() }}</h4>
                    <small>Foto Tidak Sesuai</small>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- View Toggle -->
@if($predictions->count() > 0)
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="btn-group" role="group">
            <input type="radio" class="btn-check" name="viewMode" id="gridView" autocomplete="off" checked>
            <label class="btn btn-outline-secondary" for="gridView">
                <i class="fas fa-th me-1"></i>Grid
            </label>
            <input type="radio" class="btn-check" name="viewMode" id="listView" autocomplete="off">
            <label class="btn btn-outline-secondary" for="listView">
                <i class="fas fa-list me-1"></i>List
            </label>
        </div>
        
        <div class="text-muted">
            <small>Menampilkan {{ $predictions->firstItem() }} - {{ $predictions->lastItem() }} dari {{ $predictions->total() }} hasil</small>
        </div>
    </div>

    <!-- Grid View -->
    <div id="gridViewContainer" class="row">
        @foreach($predictions as $prediction)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card prediction-card h-100 shadow-sm">
                    <div class="position-relative">
                        <img src="{{ $prediction->image_url }}" class="card-img-top" alt="Foto Air" style="height: 200px; object-fit: cover;">
                        <div class="position-absolute top-0 end-0 p-2">
                            <span class="badge {{ $prediction->water_quality_badge_class }}">
                                <i class="{{ $prediction->status_icon }} me-1"></i>
                                {{ $prediction->water_quality_label }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="card-title mb-0">ID: #{{ $prediction->id }}</h6>
                            <small class="text-muted">Kelas: {{ $prediction->predicted_class }}</small>
                        </div>
                        
                        <div class="mb-2">
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                {{ $prediction->created_at->format('d/m/Y H:i') }}
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                {{ $prediction->created_at->diffForHumans() }}
                            </small>
                        </div>
                        
                        <div class="mt-auto d-flex gap-2">
                            <a href="{{ route('predictions.show', $prediction) }}" class="btn btn-outline-primary btn-sm flex-fill">
                                <i class="fas fa-eye me-1"></i>
                                Detail
                            </a>
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="deletePrediction({{ $prediction->id }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- List View -->
    <div id="listViewContainer" class="d-none">
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="80">ID</th>
                            <th width="120">Preview</th>
                            <th>Status</th>
                            <th>Kelas</th>
                            <th>Tanggal</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($predictions as $prediction)
                            <tr>
                                <td><strong>#{{ $prediction->id }}</strong></td>
                                <td>
                                    <img src="{{ $prediction->image_url }}" alt="Preview" class="rounded" style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;" onclick="showImageModal('{{ $prediction->image_url }}', '{{ $prediction->id }}')">
                                </td>
                                <td>
                                    <span class="badge {{ $prediction->water_quality_badge_class }}">
                                        <i class="{{ $prediction->status_icon }} me-1"></i>
                                        {{ $prediction->water_quality_label }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $prediction->predicted_class }}</span>
                                </td>
                                <td>
                                    <div>{{ $prediction->created_at->format('d/m/Y H:i') }}</div>
                                    <small class="text-muted">{{ $prediction->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('predictions.show', $prediction) }}" class="btn btn-outline-primary" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" onclick="deletePrediction({{ $prediction->id }})" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $predictions->links() }}
    </div>
@else
    <!-- Empty State -->
    <div class="text-center py-5">
        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
        <h4 class="text-muted">Belum ada riwayat deteksi</h4>
        <p class="text-muted">Mulai deteksi pertama Anda dengan mengupload foto air</p>
        <a href="{{ route('predictions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            Mulai Deteksi
        </a>
    </div>
@endif

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Yakin ingin menghapus data prediksi ini?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Perhatian:</strong> Tindakan ini tidak dapat dibatalkan dan akan menghapus foto serta data prediksi secara permanen.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Batal
                </button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Preview Gambar -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Preview Gambar - ID: #<span id="imageModalId"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="/placeholder.svg" alt="Preview" class="img-fluid rounded">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const gridView = document.getElementById('gridView');
    const listView = document.getElementById('listView');
    const gridContainer = document.getElementById('gridViewContainer');
    const listContainer = document.getElementById('listViewContainer');

    // View Mode Toggle
    gridView.addEventListener('change', function() {
        if (this.checked) {
            gridContainer.classList.remove('d-none');
            listContainer.classList.add('d-none');
            localStorage.setItem('viewMode', 'grid');
        }
    });

    listView.addEventListener('change', function() {
        if (this.checked) {
            gridContainer.classList.add('d-none');
            listContainer.classList.remove('d-none');
            localStorage.setItem('viewMode', 'list');
        }
    });

    // Load saved view mode
    const savedViewMode = localStorage.getItem('viewMode');
    if (savedViewMode === 'list') {
        listView.checked = true;
        listView.dispatchEvent(new Event('change'));
    }

    // Auto-hide alerts
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});

// Single Delete Function
function deletePrediction(id) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/predictions/${id}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Show Image Modal
function showImageModal(imageUrl, predictionId) {
    document.getElementById('modalImage').src = imageUrl;
    document.getElementById('imageModalId').textContent = predictionId;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}

// Lazy loading untuk gambar
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });

    images.forEach(img => imageObserver.observe(img));
});
</script>

<style>
.prediction-card {
    transition: all 0.3s ease;
    border: 1px solid #dee2e6;
}

.prediction-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    border-color: #0d6efd;
}

.card-img-top {
    transition: transform 0.3s ease;
}

.prediction-card:hover .card-img-top {
    transform: scale(1.05);
}

.table-responsive {
    border-radius: 0.375rem;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
}

.badge {
    font-size: 0.75em;
}

/* Hover effect untuk preview gambar di table */
.table img {
    transition: transform 0.2s ease;
}

.table img:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

/* Loading state untuk gambar */
.lazy {
    background: #f8f9fa;
    min-height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.lazy::before {
    content: "Loading...";
    color: #6c757d;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
    
    .btn-group {
        width: 100%;
    }
    
    .btn-group .btn {
        flex: 1;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .card-body {
        padding: 1rem 0.75rem;
    }
}

/* Animation untuk cards */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.prediction-card {
    animation: fadeInUp 0.5s ease forwards;
}

.prediction-card:nth-child(1) { animation-delay: 0.1s; }
.prediction-card:nth-child(2) { animation-delay: 0.2s; }
.prediction-card:nth-child(3) { animation-delay: 0.3s; }
.prediction-card:nth-child(4) { animation-delay: 0.4s; }
.prediction-card:nth-child(5) { animation-delay: 0.5s; }
.prediction-card:nth-child(6) { animation-delay: 0.6s; }

/* Statistik cards hover effect */
.bg-success:hover, .bg-danger:hover, .bg-info:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease;
}
</style>
@endsection