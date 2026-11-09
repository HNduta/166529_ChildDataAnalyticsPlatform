<?php

namespace App\Http\Controllers;

use App\Models\BehaviouralRecord;
use App\Models\Prediction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClinicianDashboardController extends Controller
{
    /**
     * Display the clinician dashboard with real caseload, observation,
     * assessment, and prediction validation records.
     */
    public function index(Request $request): View
    {
        $clinician = $request->user();

        $assignedChildren = $clinician->assignedChildren()
            ->with([
                'caregiver',
                'behaviouralRecords' => fn ($q) => $q->latest('observation_date')->latest('id'),
                'predictions' => fn ($q) => $q->latest('date_generated')->latest('id'),
                'therapyGoals',
                'recommendations',
            ])
            ->latest('children.created_at')
            ->get();

        $assignedChildIds = $assignedChildren->pluck('id');

        $recentObservations = BehaviouralRecord::query()
            ->whereIn('child_id', $assignedChildIds)
            ->with(['child.caregiver', 'predictions'])
            ->latest('observation_date')
            ->latest('id')
            ->take(12)
            ->get();

        $totalObservationsCount = BehaviouralRecord::query()
            ->whereIn('child_id', $assignedChildIds)
            ->count();

        $pendingPredictions = Prediction::query()
            ->whereIn('child_id', $assignedChildIds)
            ->where('validated_by_clinician', false)
            ->with(['child.caregiver', 'observation'])
            ->latest('date_generated')
            ->latest('id')
            ->get();

        $validatedPredictions = Prediction::query()
            ->whereIn('child_id', $assignedChildIds)
            ->where('validated_by_clinician', true)
            ->with(['child.caregiver', 'observation'])
            ->latest('date_generated')
            ->latest('id')
            ->take(10)
            ->get();

        $requiredAssessmentKeys = [
            'avg_success_first_2',
            'age_years',
            'ados2_social_affect',
            'ados2_rrb',
            'cognitive_verbal_percentile',
            'cognitive_nonverbal_percentile',
            'cognitive_gca_percentile',
            'adaptive_composite_percentile',
            'num_current_services',
            'domain',
            'gender',
            'support_level',
            'primary_domain',
            'ados2_comparison_score',
        ];

        $awaitingAssessmentChildren = $assignedChildren->filter(function ($child) use ($requiredAssessmentKeys) {
            $latestObs = $child->behaviouralRecords->first();
            if (!$latestObs || !is_array($latestObs->ml_assessment_inputs)) {
                return true;
            }
            foreach ($requiredAssessmentKeys as $key) {
                if (!isset($latestObs->ml_assessment_inputs[$key]) || $latestObs->ml_assessment_inputs[$key] === '') {
                    return true;
                }
            }
            return false;
        })->values();

        return view('clinician.dashboard', compact(
            'clinician',
            'assignedChildren',
            'recentObservations',
            'totalObservationsCount',
            'pendingPredictions',
            'validatedPredictions',
            'awaitingAssessmentChildren'
        ));
    }
}