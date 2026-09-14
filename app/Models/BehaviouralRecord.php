<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BehaviouralRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'caregiver_id',
        'date_recorded',
        'communication_score',
        'social_interaction_score',
        'engagement_level',
        'repetitive_behaviour_score',
        'session_duration',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_recorded' => 'date',
            'communication_score' => 'integer',
            'social_interaction_score' => 'integer',
            'engagement_level' => 'integer',
            'repetitive_behaviour_score' => 'integer',
            'session_duration' => 'integer',
        ];
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class, 'child_id');
    }

    public function caregiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'caregiver_id');
    }

    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class, 'observation_id');
    }
}