@extends('layouts.app')

@section('title', 'Upload Foto Air')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="fas fa-camera me-2"></i>
                    Upload Foto untuk Deteksi Kualitas Air
                </h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-4">
                    <h6 class="alert-heading">
                        <i class="fas fa-info-circle me-2"></i>
                        Petunjuk Upload:
                    </h6>
                    <ul class="mb-0">
                        <li>Pastikan foto menampilkan air dengan jelas</li>
                        <li>Gunakan pencahayaan yang cukup</li>
                        <li>Format yang didukung: JPEG, PNG, JPG</li>
                        <li>Ukuran maksimal: 2MB</li>
                    </ul>
                </div>

                <form action="{{ route('predictions.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Pilih Foto Air</label>
                        <div class="upload-area" id="uploadArea">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Klik atau seret foto ke sini</h5>
                            <p class="text-muted mb-0">Format: JPEG, PNG, JPG (Maks. 50MB)</p>
                            <input type="file" name="photo" id="photo" class="d-none" accept="image/*" capture="environment"  required>
                        </div>
                        @error('photo')
                            <div class="text-danger mt-2">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div id="imagePreview" class="mb-4 d-none">
                        <label class="form-label fw-bold">Preview Gambar:</label>
                        <div class="text-center">
                            <img id="previewImg" src="/placeholder.svg" alt="Preview" class="img-fluid rounded shadow" style="max-height: 300px;">
                            <div class="mt-3">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Perhatian:</strong> Pastikan foto menampilkan air dengan jelas. Jika foto tidak sesuai, sistem akan menolak upload.
                                </div>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="changeImage">
                                    <i class="fas fa-edit me-1"></i>Ganti Gambar
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                            <span id="submitText">
                                <i class="fas fa-search me-2"></i>
                                Mulai Deteksi
                            </span>
                        </button>
                        <a href="{{ route('predictions.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Kembali ke Riwayat
                        </a>
                    </div>
                </form>

                <div class="mt-4">
                    <h6 class="text-muted">Hasil yang Mungkin:</h6>
                    <div class="row text-center">
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-body">
                                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                    <h6 class="card-title text-success">Air Bersih</h6>
                                    <small class="text-muted">Kelas: 1</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-danger">
                                <div class="card-body">
                                    <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                                    <h6 class="card-title text-danger">Air Kotor</h6>
                                    <small class="text-muted">Kelas: 2+</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-2">
                        <small class="text-muted">
                            <i class="fas fa-exclamation-triangle text-warning me-1"></i>
                            Jika foto tidak sesuai (Kelas: 0), upload akan ditolak
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('uploadArea');
    const photoInput = document.getElementById('photo');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const changeImageBtn = document.getElementById('changeImage');
    const uploadForm = document.getElementById('uploadForm');

    // Click to upload
    uploadArea.addEventListener('click', () => photoInput.click());
    changeImageBtn.addEventListener('click', () => photoInput.click());

    // Drag and drop functionality
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            photoInput.files = files;
            handleFileSelect();
        }
    });

    // File input change
    photoInput.addEventListener('change', handleFileSelect);

    function handleFileSelect() {
        const file = photoInput.files[0];
        if (file) {
            // Validate file type
            if (!file.type.startsWith('image/')) {
                showAlert('File harus berupa gambar!', 'danger');
                resetForm();
                return;
            }

            // Validate file size (2MB)
            if (file.size > 50 * 1024 * 1024) {
                showAlert('Ukuran file maksimal 50MB!', 'danger');
                resetForm();
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.classList.remove('d-none');
                submitBtn.disabled = false;
                
                // Scroll to preview
                imagePreview.scrollIntoView({ behavior: 'smooth', block: 'center' });
            };
            reader.readAsDataURL(file);
        }
    }

    function resetForm() {
        photoInput.value = '';
        imagePreview.classList.add('d-none');
        submitBtn.disabled = true;
    }

    function showAlert(message, type) {
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.temp-alert');
        existingAlerts.forEach(alert => alert.remove());

        // Create new alert
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show temp-alert`;
        alertDiv.innerHTML = `
            <i class="fas fa-exclamation-triangle me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Insert after form
        uploadForm.parentNode.insertBefore(alertDiv, uploadForm.nextSibling);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    // Form submission with loading state
    uploadForm.addEventListener('submit', function(e) {
        if (!photoInput.files[0]) {
            e.preventDefault();
            showAlert('Silakan pilih foto terlebih dahulu!', 'warning');
            return;
        }

        // Show loading state
        submitBtn.disabled = true;
        submitText.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
            Sedang Memproses...
        `;
        
        // Show processing message
        const processingAlert = document.createElement('div');
        processingAlert.className = 'alert alert-info mt-3';
        processingAlert.innerHTML = `
            <i class="fas fa-cog fa-spin me-2"></i>
            <strong>Sedang memproses foto...</strong><br>
            <small>Mohon tunggu, sistem sedang menganalisis kualitas air dari foto Anda.</small>
        `;
        uploadForm.appendChild(processingAlert);
    });

    // Prevent form resubmission on page refresh
    if (performance.navigation.type === performance.navigation.TYPE_RELOAD) {
        resetForm();
    }
});
</script>

<style>
.upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 10px;
    padding: 40px;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
    background-color: #f8f9fa;
}

.upload-area:hover {
    border-color: #0d6efd;
    background-color: #e7f3ff;
    transform: translateY(-2px);
}

.upload-area.dragover {
    border-color: #0d6efd;
    background-color: #e7f3ff;
    box-shadow: 0 0 20px rgba(13, 110, 253, 0.2);
}

#previewImg {
    border: 3px solid #dee2e6;
    transition: all 0.3s ease;
}

#previewImg:hover {
    border-color: #0d6efd;
    transform: scale(1.02);
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

@media (max-width: 768px) {
    .upload-area {
        padding: 20px;
    }
    
    .upload-area h5 {
        font-size: 1rem;
    }
    
    .upload-area p {
        font-size: 0.875rem;
    }
}
</style>
@endsection
