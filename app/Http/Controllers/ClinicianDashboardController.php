<?php

namespace App\Http\Controllers;

use App\Models\BehaviouralRecord;
use App\Models\Child;
use App\Models\Milestone;
use App\Models\Prediction;
use App\Models\TherapyGoal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClinicianDashboardController extends Controller
{
    public const REQUIRED_ASSESSMENT_KEYS = [
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

    /**
     * Ensure the authenticated clinician is assigned to the requested child.
     */
    private function authorizeAssignedChild(Request $request, Child $child): void
    {
        abort_unless(
            $request->user() && $request->user()->role === 'clinician',
            403
        );

        $assigned = $child->clinicians()
            ->where('users.id', $request->user()->id)
            ->exists();

        abort_unless($assigned, 403);
    }

    /**
     * Check whether an observation has all 14 required specialist assessment inputs.
     */
    public static function hasCompleteAssessment(?BehaviouralRecord $record): bool
    {
        if (!$record || !is_array($record->ml_assessment_inputs)) {
            return false;
        }

        foreach (self::REQUIRED_ASSESSMENT_KEYS as $key) {
            if (!isset($record->ml_assessment_inputs[$key]) || $record->ml_assessment_inputs[$key] === '') {
                return false;
            }
        }

        return true;
    }

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

        $awaitingAssessmentChildren = $assignedChildren->filter(function ($child) {
            $latestObs = $child->behaviouralRecords->first();

            return !self::hasCompleteAssessment($latestObs);
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

    /**
     * Display longitudinal behavioural scores, domain trends, therapy goals,
     * milestones, and caregiver home recommendations for an assigned child.
     */
    public function showChild(Request $request, Child $child): View
    {
        $this->authorizeAssignedChild($request, $child);

        $clinician = $request->user();

        $child->load([
            'caregiver',
            'behaviouralRecords' => fn ($q) => $q->with('predictions')->latest('observation_date')->latest('id'),
            'predictions' => fn ($q) => $q->with('observation')->latest('date_generated')->latest('id'),
            'therapyGoals' => fn ($q) => $q->with(['milestones' => fn ($m) => $m->latest('id'), 'clinician'])->latest('id'),
            'recommendations' => fn ($q) => $q->with('prediction')->latest('date_generated')->latest('id'),
        ]);

        $observations = $child->behaviouralRecords;
        $latestObservation = $observations->first();
        $latestPrediction = $child->predictions->first();
        $hasCompleteAssessment = self::hasCompleteAssessment($latestObservation);

        $domainAverages = [
            'communication' => $observations->isNotEmpty() ? round((float) $observations->avg('communication_score'), 1) : 0.0,
            'social' => $observations->isNotEmpty() ? round((float) $observations->avg('social_interaction_score'), 1) : 0.0,
            'engagement' => $observations->isNotEmpty() ? round((float) $observations->avg('engagement_level'), 1) : 0.0,
            'repetitive' => $observations->isNotEmpty() ? round((float) $observations->avg('repetitive_behaviour_score'), 1) : 0.0,
            'success_rate' => $observations->isNotEmpty() ? round((float) $observations->avg('success_rate'), 1) : 0.0,
            'session_duration' => $observations->isNotEmpty() ? round((float) $observations->avg('session_duration_minutes'), 0) : 0.0,
        ];

        $therapyGoals = $child->therapyGoals;
        $allMilestones = $therapyGoals->flatMap(fn ($g) => $g->milestones);
        $activeGoalsCount = $therapyGoals->whereIn('status', ['active', 'in_progress', 'on_track'])->count();
        $completedGoalsCount = $therapyGoals->where('status', 'completed')->count();
        $achievedMilestonesCount = $allMilestones->where('achieved_flag', true)->count();
        $totalMilestonesCount = $allMilestones->count();

        return view('clinician.children.show', compact(
            'clinician',
            'child',
            'observations',
            'latestObservation',
            'latestPrediction',
            'hasCompleteAssessment',
            'domainAverages',
            'therapyGoals',
            'activeGoalsCount',
            'completedGoalsCount',
            'achievedMilestonesCount',
            'totalMilestonesCount'
        ));
    }

    /**
     * Store a new SMART Therapy Goal for an assigned child.
     */
    public function storeGoal(Request $request, Child $child): RedirectResponse
    {
        $this->authorizeAssignedChild($request, $child);

        $validated = $request->validate([
            'goal_domain' => ['required', 'string', 'max:100'],
            'goal_description' => ['required', 'string', 'max:1000'],
            'baseline_level' => ['required', 'string', 'max:100'],
            'target_level' => ['required', 'string', 'max:100'],
            'target_date' => ['nullable', 'date'],
            'status' => ['required', 'string', 'in:active,in_progress,on_track,completed'],
        ]);

        TherapyGoal::create([
            'child_id' => $child->id,
            'clinician_id' => $request->user()->id,
            'goal_domain' => $validated['goal_domain'],
            'goal_description' => $validated['goal_description'],
            'baseline_level' => $validated['baseline_level'],
            'target_level' => $validated['target_level'],
            'target_date' => $validated['target_date'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('clinician.children.show', $child)
            ->with('success', 'Therapy goal created and shared with the caregiver.');
    }

    /**
     * Update an existing Therapy Goal's status or levels for an assigned child.
     */
    public function updateGoal(Request $request, Child $child, TherapyGoal $goal): RedirectResponse
    {
        $this->authorizeAssignedChild($request, $child);
        abort_unless((int) $goal->child_id === (int) $child->id, 404);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:active,in_progress,on_track,completed'],
            'baseline_level' => ['nullable', 'string', 'max:100'],
            'target_level' => ['nullable', 'string', 'max:100'],
            'target_date' => ['nullable', 'date'],
            'goal_description' => ['nullable', 'string', 'max:1000'],
        ]);

        $goal->update([
            'status' => $validated['status'],
            'baseline_level' => $validated['baseline_level'] ?? $goal->baseline_level,
            'target_level' => $validated['target_level'] ?? $goal->target_level,
            'target_date' => array_key_exists('target_date', $validated) && $validated['target_date'] !== null
                ? $validated['target_date']
                : $goal->target_date,
            'goal_description' => $validated['goal_description'] ?? $goal->goal_description,
        ]);

        return redirect()
            ->route('clinician.children.show', $child)
            ->with('success', 'Therapy goal progress updated.');
    }

    /**
     * Store a new Developmental Milestone under an existing Therapy Goal.
     */
    public function storeMilestone(Request $request, Child $child, TherapyGoal $goal): RedirectResponse
    {
        $this->authorizeAssignedChild($request, $child);
        abort_unless((int) $goal->child_id === (int) $child->id, 404);

        $validated = $request->validate([
            'milestone_description' => ['required', 'string', 'max:500'],
            'achieved_flag' => ['nullable', 'boolean'],
        ]);

        $achieved = $request->boolean('achieved_flag');

        Milestone::create([
            'goal_id' => $goal->id,
            'child_id' => $child->id,
            'milestone_description' => $validated['milestone_description'],
            'achieved_flag' => $achieved,
            'date_achieved' => $achieved ? now()->toDateString() : null,
            'recorded_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('clinician.children.show', $child)
            ->with('success', 'Developmental milestone added to goal.');
    }

    /**
     * Toggle or update a Developmental Milestone's achievement flag.
     */
    public function toggleMilestone(Request $request, Child $child, Milestone $milestone): RedirectResponse
    {
        $this->authorizeAssignedChild($request, $child);
        abort_unless((int) $milestone->child_id === (int) $child->id, 404);

        $newFlag = $request->has('achieved_flag')
            ? $request->boolean('achieved_flag')
            : !$milestone->achieved_flag;

        $milestone->update([
            'achieved_flag' => $newFlag,
            'date_achieved' => $newFlag ? now()->toDateString() : null,
            'recorded_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('clinician.children.show', $child)
            ->with('success', $newFlag
                ? 'Developmental milestone marked as achieved.'
                : 'Developmental milestone reopened as in progress.');
    }
}