<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prediction extends Model
{
    use HasFactory;
    protected $fillable = [
        'image_path',
        'predicted_class',
        'water_quality'
    ];

    protected $casts = [
        'predicted_class' => 'integer',
    ];

    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }

    public function getWaterQualityLabelAttribute()
{
    return match ($this->predicted_class) {
        0 => 'Air Bersih',
        1 => 'Air Keruh',
        2 => 'Air Kotor',
        3 => 'Foto Tidak Sesuai',
        default => 'Tidak Diketahui',
    };
}

public function getWaterQualityBadgeClassAttribute()
{
    return match ($this->predicted_class) {
        0 => 'bg-success',
        1 => 'bg-warning text-dark',
        2 => 'bg-danger',
        3 => 'bg-secondary',
        default => 'bg-light',
    };
}

public function getStatusIconAttribute()
{
    return match ($this->predicted_class) {
        0 => 'fas fa-check-circle',
        1 => 'fas fa-water',
        2 => 'fas fa-times-circle',
        3 => 'fas fa-exclamation-triangle',
        default => 'fas fa-question-circle',
    };
}

public function getWaterQualityColorAttribute()
{
    return match ($this->predicted_class) {
        0 => 'text-success',
        1 => 'text-warning',
        2 => 'text-danger',
        3 => 'text-secondary',
        default => 'text-muted',
    };
}

public function getInterpretationAlertClassAttribute()
{
    return match ($this->predicted_class) {
        0 => 'alert-success',
        1 => 'alert-warning',
        2 => 'alert-danger',
        3 => 'alert-secondary',
        default => 'alert-light',
    };
}

public function getInterpretationMessageAttribute()
{
    return match ($this->predicted_class) {
        0 => 'Air terdeteksi dalam kondisi <strong>bersih</strong>. Air ini kemungkinan aman untuk digunakan.',
        1 => 'Air terdeteksi dalam kondisi <strong>keruh</strong>. Sebaiknya lakukan penyaringan atau pemeriksaan lanjutan.',
        2 => 'Air terdeteksi dalam kondisi <strong>kotor</strong>. Tidak disarankan untuk digunakan langsung.',
        3 => 'Foto tidak sesuai untuk dianalisis. Silakan unggah ulang foto dengan kondisi air yang jelas.',
        default => 'Data tidak dapat diinterpretasi.',
    };
}

}
