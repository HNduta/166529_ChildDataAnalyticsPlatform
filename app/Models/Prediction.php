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

    /**
     * Generate and store transparent, non-diagnostic support recommendations
     * mapped from this prediction outcome and assessment context.
     */
    public function syncSupportRecommendations(array $inputs = []): void
    {
        if ($this->recommendations()->exists()) {
            return;
        }

        $domain = ucfirst((string) ($inputs['domain'] ?? $inputs['primary_domain'] ?? 'Communication'));
        $isHighSupport = (string) $this->predicted_outcome === '0';

        $items = $isHighSupport
            ? [
                [
                    'category' => $domain,
                    'recommendation_text' => 'Increase structured visual scaffolding and step-by-step prompting during ' . strtolower($domain) . ' activities, breaking tasks into short 10-15 minute intervals.',
                    'status' => 'active',
                ],
                [
                    'category' => 'Sensory & Regulation',
                    'recommendation_text' => 'Incorporate scheduled sensory decompression breaks and visual countdown timers before routine transitions to support emotional regulation.',
                    'status' => 'active',
                ],
            ]
            : [
                [
                    'category' => $domain,
                    'recommendation_text' => 'Maintain current structured ' . strtolower($domain) . ' routines while gradually fading verbal prompts to encourage independent skill generalization.',
                    'status' => 'active',
                ],
                [
                    'category' => 'Social Reciprocity',
                    'recommendation_text' => 'Introduce guided peer or caregiver turn-taking activities to build on positive session engagement trends.',
                    'status' => 'active',
                ],
            ];

        foreach ($items as $item) {
            $this->recommendations()->create([
                'child_id' => $this->child_id,
                'recommendation_text' => $item['recommendation_text'],
                'category' => $item['category'],
                'date_generated' => ($this->date_generated ?? now())->toDateString(),
                'status' => $item['status'],
            ]);
        }
    }
}