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
        'filename',
        'result',
        'status',
        'confidence',
        'prediction_class',
        'prediction_index',
        'class_probabilities'
    ];

    protected $casts = [
        'result' => 'array',
        'class_probabilities' => 'array',
        'confidence' => 'decimal:2'
    ];

    // Class labels
    private $classLabels = [
        0 => 'Bersih',
        1 => 'Sedang', 
        2 => 'Keruh'
    ];

    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }

    public function getFormattedCreatedAtAttribute()
    {
        return Carbon::parse($this->created_at)->format('d M Y H:i');
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->prediction_class) {
            'Bersih' => 'success',
            'Sedang' => 'warning',
            'Keruh' => 'danger',
            default => 'secondary'
        };
    }

    public function getClassColorAttribute()
    {
        return match($this->prediction_class) {
            'Bersih' => 'success',
            'Sedang' => 'warning', 
            'Keruh' => 'danger',
            default => 'secondary'
        };
    }

    public function getProbabilitiesWithLabelsAttribute()
    {
        if (!$this->class_probabilities) {
            return [];
        }

        $probabilities = [];
        foreach ($this->class_probabilities as $index => $probability) {
            $probabilities[] = [
                'label' => $this->classLabels[$index] ?? "Class $index",
                'probability' => $probability,
                'percentage' => number_format($probability * 100, 2)
            ];
        }

        return $probabilities;
    }

    public function getConfidenceLevelAttribute()
    {
        if ($this->confidence >= 90) return 'Sangat Tinggi';
        if ($this->confidence >= 80) return 'Tinggi';
        if ($this->confidence >= 70) return 'Sedang';
        if ($this->confidence >= 60) return 'Rendah';
        return 'Sangat Rendah';
    }
}
