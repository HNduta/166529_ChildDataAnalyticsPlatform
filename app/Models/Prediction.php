<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'observation_id',
        'model_used',
        'predicted_outcome',
        'confidence_score',
        'date_generated',
        'validated_by_clinician',
        'clinician_feedback',
    ];

    protected function casts(): array
    {
        return [
            'confidence_score' => 'decimal:4',
            'date_generated' => 'datetime',
            'validated_by_clinician' => 'boolean',
        ];
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class, 'child_id');
    }

    public function observation(): BelongsTo
    {
        return $this->belongsTo(BehaviouralRecord::class, 'observation_id');
    }

    public function recommendations(): HasMany
    {
        return $this->hasMany(Recommendation::class, 'prediction_id');
    }
}