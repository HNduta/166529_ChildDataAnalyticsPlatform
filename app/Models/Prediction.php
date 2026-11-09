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
     * Return a caregiver-friendly label for the predicted outcome class.
     * Class 0 = On-Track (Improving / Slow Progress)
     * Class 1 = May Need Additional Support (At-Risk: Regression / Plateau)
     */
    public function caregiverIndicatorLabel(): string
    {
        return match ((string) $this->predicted_outcome) {
            '0' => 'On-Track',
            '1' => 'May Need Additional Support',
            default => 'Pending Assessment',
        };
    }

    /**
     * Return a plain-language caregiver explanation of the indicator.
     */
    public function caregiverIndicatorDescription(): string
    {
        return match ((string) $this->predicted_outcome) {
            '0' => 'Recorded observations and specialist assessment inputs align with steady or gradual developmental progress. This exploratory indicator does not mean a child has no developmental difficulties, and a qualified clinician should always interpret the results.',
            '1' => 'Recorded observations and specialist assessment inputs suggest areas where progress may have plateaued or where extra structured support could be helpful. This exploratory indicator is not a medical diagnosis and should be discussed with a qualified clinician.',
            default => 'An exploratory developmental indicator will appear automatically once complete assessment information is available.',
        };
    }

    /**
     * Generate and store transparent, non-diagnostic support recommendations
     * mapped from this prediction outcome and assessment context using
     * simple, caregiver-friendly language.
     *
     * Class 0: On-Track (Improving and Slow Progress)
     * Class 1: At-Risk / May Need Additional Support (Regression and Plateau)
     */
    public function syncSupportRecommendations(array $inputs = []): void
    {
        if ($this->recommendations()->exists()) {
            return;
        }

        $rawDomain = strtolower(trim((string) ($inputs['domain'] ?? $inputs['primary_domain'] ?? 'communication')));
        $domainLabel = match ($rawDomain) {
            'communication', 'speech', 'language' => 'Communication',
            'social', 'social_interaction', 'social interaction' => 'Social Interaction',
            'adaptive', 'independence', 'daily_living', 'self_care' => 'Everyday Independence',
            'cognitive', 'engagement', 'motor' => 'Engagement and Participation',
            default => ucfirst($rawDomain ?: 'Communication'),
        };

        $isAdditionalSupportNeeded = (string) $this->predicted_outcome === '1';

        $items = $isAdditionalSupportNeeded
            ? [
                [
                    'category' => $domainLabel,
                    'recommendation_text' => 'Practice short, 10-minute ' . strtolower($domainLabel) . ' activities at home using simple picture cards or gestures, and pause to celebrate every small response.',
                    'status' => 'active',
                ],
                [
                    'category' => 'Calm Routines & Transitions',
                    'recommendation_text' => 'Give a gentle 5-minute and 2-minute visual or verbal heads-up before changing activities, paired with a quiet comfort break when needed.',
                    'status' => 'active',
                ],
            ]
            : [
                [
                    'category' => $domainLabel,
                    'recommendation_text' => 'Keep up your daily ' . strtolower($domainLabel) . ' routines while giving your child a few extra seconds to try the first step on their own.',
                    'status' => 'active',
                ],
                [
                    'category' => 'Social Interaction',
                    'recommendation_text' => 'Play relaxed back-and-forth turn-taking games (like rolling a ball, stacking blocks, or sharing a storybook) to build on your child\'s steady engagement.',
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