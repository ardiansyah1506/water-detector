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
        switch ($this->predicted_class) {
            case 0:
                return 'Foto Tidak Sesuai';
                case 1:
                    return 'Air Kotor';
                case 2:
                    return 'Air Bersih';
                default:
                return 'Foto Tidak Sesuai';
        }
    }

    public function getWaterQualityBadgeClassAttribute()
    {
        switch ($this->predicted_class) {
            case 0:
                return 'badge-warning';
                case 1:
                    return 'badge-danger';
                default:
                return 'badge-success';
        }
    }

    public function getStatusIconAttribute()
    {
        switch ($this->predicted_class) {
            case 0:
                return 'fas fa-exclamation-triangle';
                case 1:
                    return 'fas fa-times-circle';
                    default:
                    return 'fas fa-check-circle';
        }
    }
}
