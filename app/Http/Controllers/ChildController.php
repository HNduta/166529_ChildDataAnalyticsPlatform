<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Services\ChildPredictionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class ChildController extends Controller
{
    /**
     * Display the authenticated caregiver's children.
     */
    public function index(Request $request): View
    {
        $query = $request->user()
            ->children()
            ->latest();

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('sex', 'like', '%' . $search . '%');
            });
        }

        $children = $query->paginate(10)->withQueryString();

        return view('caregiver.children.index', compact('children'));
    }

    /**
     * Redirect caregiver to their active child's recommendations page.
     */
    public function caregiverRecommendations(Request $request): RedirectResponse
    {
        $child = $request->user()->children()->latest()->first();

        if ($child) {
            return redirect()->route('children.recommendations', $child);
        }

        return redirect()
            ->route('children.index')
            ->with('info', 'Please select or create a child profile to view support recommendations.');
    }

    /**
     * Show the form for creating a child profile.
     */
    public function create(): View
    {
        return view('caregiver.children.create');
    }

    /**
     * Store a new child profile.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'age_in_months' => ['required', 'integer', 'min:0', 'max:216'],
            'sex' => ['required', 'in:Male,Female,Other'],
            'born_with_jaundice' => ['nullable', 'boolean'],
            'family_member_with_asd' => ['nullable', 'boolean'],
            'diagnosis_confirmed' => ['nullable', 'boolean'],
        ]);

        $validated['born_with_jaundice'] =
            $request->boolean('born_with_jaundice');

        $validated['family_member_with_asd'] =
            $request->boolean('family_member_with_asd');

        $validated['diagnosis_confirmed'] =
            $request->boolean('diagnosis_confirmed');

        // Assign ownership using the authenticated caregiver.
        $request->user()->children()->create($validated);

        return redirect()
            ->route('children.index')
            ->with('success', 'Child profile created successfully.');
    }

    /**
     * Display a child profile.
     */
    public function show(Request $request, Child $child): View
    {
        if ($request->query('tab') === 'ml-predictions-analytics') {
            return $this->predictionsAnalytics($request, $child);
        }
        if ($request->query('tab') === 'goals-milestones') {
            return $this->goalsMilestones($request, $child);
        }
        if ($request->query('tab') === 'recommendations') {
            return $this->recommendations($request, $child);
        }

        $child = $request->user()
            ->children()
            ->findOrFail($child->id);

        $recordsQuery = $child->behaviouralRecords()
            ->latest('observation_date')
            ->latest('id');

        if ($request->filled('observation_date')) {
            $recordsQuery->whereDate('observation_date', $request->observation_date);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $recordsQuery->where(function ($q) use ($search) {
                $q->where('notes', 'like', '%' . $search . '%')
                  ->orWhere('observation_date', 'like', '%' . $search . '%')
                  ->orWhere('success_rate', 'like', '%' . $search . '%');
            });
        }

        $records = $recordsQuery->paginate(10)->withQueryString();
        $recentRecordsQuery = $child->behaviouralRecords()->latest('observation_date')->latest('id');
        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $recentRecordsQuery->where(function ($q) use ($search) {
                $q->where('notes', 'like', '%' . $search . '%')
                  ->orWhere('observation_date', 'like', '%' . $search . '%')
                  ->orWhere('success_rate', 'like', '%' . $search . '%');
            });
        }
        $recentRecords = $recentRecordsQuery->take(5)->get();

        $allChildRecords = $child->behaviouralRecords();
        $totalRecords = (clone $allChildRecords)->count();
        $averageSuccessRate = (clone $allChildRecords)->avg('success_rate');
        $averageDuration = (clone $allChildRecords)->avg('session_duration_minutes');

        $therapyGoalsCount = $child->therapyGoals()->count();
        $recommendationsCount = $child->recommendations()->count();
        $latestPrediction = $child->predictions()->latest('date_generated')->first();
        $clinicians = $child->clinicians()->get();

        return view('caregiver.children.show', compact(
            'child',
            'records',
            'recentRecords',
            'totalRecords',
            'averageSuccessRate',
            'averageDuration',
            'therapyGoalsCount',
            'recommendationsCount',
            'latestPrediction',
            'clinicians'
        ));
    }

    /**
     * Display the child's Goals and Milestones page.
     */
    public function goalsMilestones(Request $request, Child $child): View
    {
        $child = $request->user()
            ->children()
            ->findOrFail($child->id);

        $therapyGoals = $child->therapyGoals()
            ->with(['milestones', 'clinician'])
            ->latest()
            ->get();

        $milestones = $child->milestones()
            ->with('goal')
            ->latest()
            ->get();

        $activeGoalsCount = $therapyGoals->whereIn('status', ['active', 'in_progress', 'on_track'])->count();
        $completedGoalsCount = $therapyGoals->where('status', 'completed')->count();
        $completedMilestonesCount = $milestones->where('achieved_flag', true)->count();

        $clinicalValidations = $child->predictions()
            ->where('validated_by_clinician', true)
            ->latest('date_generated')
            ->get();

        return view('caregiver.children.goals-milestones', compact(
            'child',
            'therapyGoals',
            'milestones',
            'activeGoalsCount',
            'completedGoalsCount',
            'completedMilestonesCount',
            'clinicalValidations'
        ));
    }

    /**
     * Display the child's Support Recommendations page.
     */
    public function recommendations(Request $request, Child $child): View
    {
        $child = $request->user()
            ->children()
            ->findOrFail($child->id);

        $latestPredForRecs = $child->predictions()->latest('date_generated')->first();
        if ($latestPredForRecs && !$child->recommendations()->exists()) {
            $latestInputs = $child->behaviouralRecords()
                ->whereNotNull('ml_assessment_inputs')
                ->latest('observation_date')
                ->first()?->ml_assessment_inputs ?? [];
            $latestPredForRecs->syncSupportRecommendations($latestInputs);
        }

        $recommendations = $child->recommendations()
            ->with('prediction')
            ->latest('date_generated')
            ->get();

        $activeCount = $recommendations->whereIn('status', ['active', 'urgent', 'new'])->count();
        $implementedCount = $recommendations->where('status', 'implemented')->count();

        $allChildRecords = $child->behaviouralRecords();
        $childTotalRecords = (clone $allChildRecords)->count();
        $childAvgSuccess = (clone $allChildRecords)->avg('success_rate');
        $avgCommunication = (clone $allChildRecords)->avg('communication_score');
        $avgSocial = (clone $allChildRecords)->avg('social_interaction_score');
        $avgEngagement = (clone $allChildRecords)->avg('engagement_level');
        $avgRepetitive = (clone $allChildRecords)->avg('repetitive_behaviour_score');

        return view('caregiver.children.recommendations', compact(
            'child',
            'recommendations',
            'activeCount',
            'implementedCount',
            'childTotalRecords',
            'childAvgSuccess',
            'avgCommunication',
            'avgSocial',
            'avgEngagement',
            'avgRepetitive'
        ));
    }

    /**
     * Display the child's ML Predictions and Analytics page.
     */
    public function predictionsAnalytics(Request $request, Child $child): View
    {
        $child = $request->user()
            ->children()
            ->findOrFail($child->id);

        $allChildRecords = $child->behaviouralRecords();
        $totalRecords = (clone $allChildRecords)->count();
        $averageSuccessRate = (clone $allChildRecords)->avg('success_rate');
        $averageDuration = (clone $allChildRecords)->avg('session_duration_minutes');

        $avgCommunication = (clone $allChildRecords)->avg('communication_score');
        $avgSocial = (clone $allChildRecords)->avg('social_interaction_score');
        $avgEngagement = (clone $allChildRecords)->avg('engagement_level');
        $avgRepetitive = (clone $allChildRecords)->avg('repetitive_behaviour_score');

        $trajectoryRecords = $child->behaviouralRecords()
            ->oldest('observation_date')
            ->oldest('id')
            ->get();

        $firstTwoRecords = $child->behaviouralRecords()
            ->oldest('observation_date')
            ->oldest('id')
            ->take(2)
            ->get();
        $avgSuccessFirst2 = $firstTwoRecords->count() >= 2
            ? $firstTwoRecords->avg('success_rate')
            : ($firstTwoRecords->count() == 1 ? $firstTwoRecords->first()->success_rate : null);

        $latestRecordWithAssessment = $child->behaviouralRecords()
            ->whereNotNull('ml_assessment_inputs')
            ->latest('observation_date')
            ->first();
        $assessmentInputs = $latestRecordWithAssessment?->ml_assessment_inputs ?? [];

        $childAgeYears = $child->age_in_months
            ? round($child->age_in_months / 12, 1)
            : 4.0;
        $childGenderVal = strtolower($child->sex ?? 'female');

        if (!isset($assessmentInputs['avg_success_first_2']) && $avgSuccessFirst2 !== null) {
            $assessmentInputs['avg_success_first_2'] = round((float) $avgSuccessFirst2, 1);
        }
        if (!isset($assessmentInputs['age_years'])) {
            $assessmentInputs['age_years'] = $childAgeYears;
        }
        if (!isset($assessmentInputs['gender'])) {
            $assessmentInputs['gender'] = $childGenderVal;
        }

        $requiredFeatures = [
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

        $missingFeatures = [];
        foreach ($requiredFeatures as $featKey) {
            if (!isset($assessmentInputs[$featKey]) || $assessmentInputs[$featKey] === '') {
                $missingFeatures[] = $featKey;
            }
        }

        $mlServiceResult = session('ml_service_result');
        $mlServiceError = session('ml_service_error');

        if (!$mlServiceResult && empty($missingFeatures)) {
            try {
                $mlServiceResult = app(ChildPredictionService::class)->predict($assessmentInputs);

                $targetObservation = $latestRecordWithAssessment
                    ?? $child->behaviouralRecords()->latest('observation_date')->first();

                if ($targetObservation && !$child->predictions()->exists()) {
                    $predictedKey = (string) $mlServiceResult['predicted_class'];
                    $confScore = isset($mlServiceResult['class_scores'][$predictedKey])
                        ? round((float) $mlServiceResult['class_scores'][$predictedKey], 4)
                        : null;

                    $createdPred = $targetObservation->predictions()->create([
                        'child_id' => $child->id,
                        'model_used' => 'child_intervention_pipeline',
                        'predicted_outcome' => $predictedKey,
                        'confidence_score' => $confScore,
                        'date_generated' => now(),
                        'validated_by_clinician' => false,
                    ]);
                    $createdPred->syncSupportRecommendations($assessmentInputs);
                }
            } catch (Throwable $e) {
                $mlServiceError = $e->getMessage();
            }
        }

        $latestPrediction = $child->predictions()->latest('date_generated')->first();
        $predictions = $child->predictions()->with('observation')->latest('date_generated')->get();
        $clinicalValidations = $child->predictions()
            ->where('validated_by_clinician', true)
            ->with('observation')
            ->latest('date_generated')
            ->get();

        $therapyGoals = $child->therapyGoals()->with('milestones')->get();
        $clinicians = $child->clinicians()->get();

        return view('caregiver.children.predictions-analytics', compact(
            'child',
            'totalRecords',
            'averageSuccessRate',
            'averageDuration',
            'predictions',
            'latestPrediction',
            'clinicalValidations',
            'therapyGoals',
            'clinicians',
            'trajectoryRecords',
            'avgSuccessFirst2',
            'assessmentInputs',
            'missingFeatures',
            'mlServiceResult',
            'mlServiceError',
            'avgCommunication',
            'avgSocial',
            'avgEngagement',
            'avgRepetitive'
        ));
    }

    /**
     * Submit assessment inputs and generate a prediction from the Python ML service.
     */
    public function generatePrediction(
        Request $request,
        Child $child,
        ChildPredictionService $predictionService
    ): RedirectResponse {
        $child = $request->user()
            ->children()
            ->findOrFail($child->id);

        $validated = $request->validate([
            'avg_success_first_2' => ['required', 'numeric', 'between:0,100'],
            'age_years' => ['required', 'numeric', 'between:0,120'],
            'ados2_social_affect' => ['required', 'numeric'],
            'ados2_rrb' => ['required', 'numeric'],
            'cognitive_verbal_percentile' => ['required', 'numeric', 'between:0,100'],
            'cognitive_nonverbal_percentile' => ['required', 'numeric', 'between:0,100'],
            'cognitive_gca_percentile' => ['required', 'numeric', 'between:0,100'],
            'adaptive_composite_percentile' => ['required', 'numeric', 'between:0,100'],
            'num_current_services' => ['required', 'integer', 'min:0'],
            'domain' => ['required', 'string', 'min:1', 'max:100'],
            'gender' => ['required', 'string', 'min:1', 'max:50'],
            'support_level' => ['required', 'string', 'min:1', 'max:100'],
            'primary_domain' => ['required', 'string', 'min:1', 'max:100'],
            'ados2_comparison_score' => ['required', 'string', 'min:1', 'max:100'],
        ]);

        $validated['avg_success_first_2'] = (float) $validated['avg_success_first_2'];
        $validated['age_years'] = (float) $validated['age_years'];
        $validated['ados2_social_affect'] = (float) $validated['ados2_social_affect'];
        $validated['ados2_rrb'] = (float) $validated['ados2_rrb'];
        $validated['cognitive_verbal_percentile'] = (float) $validated['cognitive_verbal_percentile'];
        $validated['cognitive_nonverbal_percentile'] = (float) $validated['cognitive_nonverbal_percentile'];
        $validated['cognitive_gca_percentile'] = (float) $validated['cognitive_gca_percentile'];
        $validated['adaptive_composite_percentile'] = (float) $validated['adaptive_composite_percentile'];
        $validated['num_current_services'] = (int) $validated['num_current_services'];

        $record = $child->behaviouralRecords()->latest('observation_date')->first();
        if (!$record) {
            $record = $child->behaviouralRecords()->create([
                'caregiver_id' => $request->user()->id,
                'observation_date' => now()->toDateString(),
                'communication_score' => 3,
                'social_interaction_score' => 3,
                'engagement_level' => 3,
                'repetitive_behaviour_score' => 2,
                'session_duration_minutes' => 30,
                'success_rate' => $validated['avg_success_first_2'],
                'notes' => 'Baseline observation created for ML assessment',
                'ml_assessment_inputs' => $validated,
            ]);
        } else {
            $record->update([
                'ml_assessment_inputs' => $validated,
            ]);
        }

        try {
            $result = $predictionService->predict($validated);

            $predictedKey = (string) $result['predicted_class'];
            $confidenceScore = isset($result['class_scores'][$predictedKey])
                ? round((float) $result['class_scores'][$predictedKey], 4)
                : null;

            $prediction = $record->predictions()->create([
                'child_id' => $child->id,
                'model_used' => 'child_intervention_pipeline',
                'predicted_outcome' => $predictedKey,
                'confidence_score' => $confidenceScore,
                'date_generated' => now(),
                'validated_by_clinician' => false,
            ]);
            $prediction->syncSupportRecommendations($validated);

            return redirect()
                ->route('children.predictions-analytics', $child)
                ->with('success', 'ML service prediction generated and recorded successfully.')
                ->with('ml_service_result', $result);
        } catch (Throwable $e) {
            return redirect()
                ->route('children.predictions-analytics', $child)
                ->with('ml_service_error', 'Assessment inputs saved. Could not reach ML service (' . $e->getMessage() . '). Ensure the FastAPI service is running.');
        }
    }

    /**
     * Show the form for editing a child profile.
     */
    public function edit(Request $request, Child $child): View
    {
        $child = $request->user()
            ->children()
            ->findOrFail($child->id);

        return view('caregiver.children.edit', compact('child'));
    }

    /**
     * Update a child profile.
     */
    public function update(
        Request $request,
        Child $child
    ): RedirectResponse {
        $child = $request->user()
            ->children()
            ->findOrFail($child->id);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'age_in_months' => ['required', 'integer', 'min:0', 'max:216'],
            'sex' => ['required', 'in:Male,Female,Other'],
            'born_with_jaundice' => ['nullable', 'boolean'],
            'family_member_with_asd' => ['nullable', 'boolean'],
            'diagnosis_confirmed' => ['nullable', 'boolean'],
        ]);

        $validated['born_with_jaundice'] =
            $request->boolean('born_with_jaundice');

        $validated['family_member_with_asd'] =
            $request->boolean('family_member_with_asd');

        $validated['diagnosis_confirmed'] =
            $request->boolean('diagnosis_confirmed');

        $child->update($validated);

        return redirect()
            ->route('children.show', $child)
            ->with('success', 'Child profile updated successfully.');
    }

    /**
     * Delete a child profile.
     */
    public function destroy(
        Request $request,
        Child $child
    ): RedirectResponse {
        $child = $request->user()
            ->children()
            ->findOrFail($child->id);

        $child->delete();

        return redirect()
            ->route('children.index')
            ->with('success', 'Child profile deleted successfully.');
    }
}