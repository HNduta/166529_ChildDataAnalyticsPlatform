<?php

namespace App\Services;

use App\Models\BehaviouralRecord;
use App\Models\User;
use Illuminate\Support\Collection;

class BehaviouralAnalyticsData
{
    /**
     * Retrieve a caregiver's observations for one of their children.
     *
     * Results are ordered from oldest to newest for time-based analysis.
     */
    public function forChild(User $caregiver, int $childId): Collection
    {
        // Check ownership before retrieving any observations.
        $caregiver->children()->findOrFail($childId);

        return BehaviouralRecord::query()
            ->where('caregiver_id', $caregiver->id)
            ->where('child_id', $childId)
            ->orderBy('observation_date')
            ->orderBy('id')
            ->get([
                'id',
                'child_id',
                'observation_date',
                'communication_score',
                'social_interaction_score',
                'engagement_level',
                'repetitive_behaviour_score',
                'success_rate',
                'session_duration_minutes',
                'eye_contact_rating',
                'prompts_required',
            ])
            ->map(function (BehaviouralRecord $record): array {
                return [
                    'observation_id' => $record->id,
                    'child_id' => $record->child_id,
                    'observation_date' => $record->observation_date
                        ? $record->observation_date->toDateString()
                        : null,
                    'features' => [
                        'communication_score' =>
                            $record->communication_score,
                        'social_interaction_score' =>
                            $record->social_interaction_score,
                        'engagement_level' =>
                            $record->engagement_level,
                        'repetitive_behaviour_score' =>
                            $record->repetitive_behaviour_score,
                        'success_rate' =>
                            $record->success_rate,
                        'session_duration_minutes' =>
                            $record->session_duration_minutes,
                        'eye_contact_rating' =>
                            $record->eye_contact_rating,
                        'prompts_required' =>
                            $record->prompts_required,
                    ],
                ];
            });
    }
}