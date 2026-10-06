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
        'observation_date',
        'success_rate',
        'engagement_level',
        'prompts_required',
        'eye_contact_rating',
        'session_duration_minutes',
        'communication_score',
        'social_interaction_score',
        'repetitive_behaviour_score',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'observation_date' => 'date',
            'success_rate' => 'decimal:2',
            'engagement_level' => 'integer',
            'prompts_required' => 'decimal:2',
            'eye_contact_rating' => 'integer',
            'session_duration_minutes' => 'integer',
            'communication_score' => 'integer',
            'social_interaction_score' => 'integer',
            'repetitive_behaviour_score' => 'integer',
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