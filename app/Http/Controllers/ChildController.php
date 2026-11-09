<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\Recommendation;
use App\Services\ChildPredictionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            'profile_picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        unset($validated['profile_picture']);

        $validated['born_with_jaundice'] =
            $request->boolean('born_with_jaundice');

        $validated['family_member_with_asd'] =
            $request->boolean('family_member_with_asd');

        $validated['diagnosis_confirmed'] =
            $request->boolean('diagnosis_confirmed');

        if ($request->hasFile('profile_picture')) {
            $validated['profile_picture'] = $request->file('profile_picture')->store('profile-pictures/children', 'public');
        }

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
            ? round((float) $firstTwoRecords->avg('success_rate'), 1)
            : null;

        $latestObservation = $child->behaviouralRecords()
            ->latest('observation_date')
            ->latest('id')
            ->first();

        $latestRecordWithAssessment = $child->behaviouralRecords()
            ->whereNotNull('ml_assessment_inputs')
            ->latest('observation_date')
            ->latest('id')
            ->first();
        $assessmentInputs = is_array($latestRecordWithAssessment?->ml_assessment_inputs)
            ? $latestRecordWithAssessment->ml_assessment_inputs
            : [];

        if ($latestRecordWithAssessment !== null) {
            if ((!isset($assessmentInputs['avg_success_first_2']) || $assessmentInputs['avg_success_first_2'] === '') && $avgSuccessFirst2 !== null) {
                $assessmentInputs['avg_success_first_2'] = $avgSuccessFirst2;
            }
            if ((!isset($assessmentInputs['age_years']) || $assessmentInputs['age_years'] === '') && $child->age_in_months !== null) {
                $assessmentInputs['age_years'] = round($child->age_in_months / 12, 1);
            }
            if ((!isset($assessmentInputs['gender']) || $assessmentInputs['gender'] === '') && !empty($child->sex)) {
                $assessmentInputs['gender'] = strtolower($child->sex);
            }
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

        $mlServiceResult = null;
        $mlServiceError = null;
        $mlServiceUnavailable = false;

        $latestExistingPrediction = $child->predictions()
            ->latest('date_generated')
            ->latest('id')
            ->first();

        $latestObservationUpdatedAt = $child->behaviouralRecords()->max('updated_at');

        $needsNewPrediction = false;
        if ($latestRecordWithAssessment !== null && empty($missingFeatures)) {
            if ($latestExistingPrediction === null) {
                $needsNewPrediction = true;
            } elseif ((int) $latestExistingPrediction->observation_id !== (int) $latestRecordWithAssessment->id) {
                $needsNewPrediction = true;
            } elseif (
                $latestRecordWithAssessment->updated_at
                && $latestExistingPrediction->date_generated
                && $latestRecordWithAssessment->updated_at->gt($latestExistingPrediction->date_generated)
            ) {
                $needsNewPrediction = true;
            } elseif (
                $latestObservationUpdatedAt
                && $latestExistingPrediction->date_generated
                && \Carbon\Carbon::parse($latestObservationUpdatedAt)->gt($latestExistingPrediction->date_generated)
            ) {
                $needsNewPrediction = true;
            }
        }

        if ($needsNewPrediction) {
            try {
                $mlServiceResult = app(ChildPredictionService::class)->predict($assessmentInputs);

                $predictedKey = (string) $mlServiceResult['predicted_class'];
                $confScore = isset($mlServiceResult['class_scores'][$predictedKey])
                    ? round((float) $mlServiceResult['class_scores'][$predictedKey], 4)
                    : null;

                $createdPred = $latestRecordWithAssessment->predictions()->create([
                    'child_id' => $child->id,
                    'model_used' => 'child_intervention_pipeline',
                    'predicted_outcome' => $predictedKey,
                    'confidence_score' => $confScore,
                    'date_generated' => now(),
                    'validated_by_clinician' => false,
                ]);
                $createdPred->syncSupportRecommendations($assessmentInputs);
            } catch (\Illuminate\Validation\ValidationException $e) {
                $mlServiceError = 'The recorded assessment inputs contain invalid values and could not be processed. Please ask your clinician to review the assessment record.';
            } catch (Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Automatic prediction request failed.', [
                    'child_id' => $child->id,
                    'observation_id' => $latestRecordWithAssessment?->id,
                    'exception' => $e::class,
                ]);
                $mlServiceUnavailable = true;
                $mlServiceError = 'The developmental progress analysis service is temporarily unavailable. No new indicator was generated; please try again shortly.';
            }
        } elseif ($latestExistingPrediction && !$child->recommendations()->exists()) {
            $latestExistingPrediction->syncSupportRecommendations($assessmentInputs);
        }

        $latestPrediction = $child->predictions()->latest('date_generated')->latest('id')->first();
        $predictions = $child->predictions()->with('observation')->latest('date_generated')->latest('id')->get();
        $clinicalValidations = $child->predictions()
            ->where('validated_by_clinician', true)
            ->with('observation')
            ->latest('date_generated')
            ->get();

        $recommendations = $child->recommendations()
            ->with('prediction')
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
            'latestObservation',
            'latestRecordWithAssessment',
            'clinicalValidations',
            'recommendations',
            'therapyGoals',
            'clinicians',
            'trajectoryRecords',
            'avgSuccessFirst2',
            'assessmentInputs',
            'missingFeatures',
            'mlServiceResult',
            'mlServiceError',
            'mlServiceUnavailable',
            'avgCommunication',
            'avgSocial',
            'avgEngagement',
            'avgRepetitive'
        ));
    }

    /**
     * Update a recommendation's home practice status for the caregiver's child.
     */
    public function updateRecommendationStatus(
        Request $request,
        Child $child,
        Recommendation $recommendation
    ): RedirectResponse {
        $child = $request->user()
            ->children()
            ->findOrFail($child->id);

        abort_unless((int) $recommendation->child_id === (int) $child->id, 404);

        $validated = $request->validate([
            'status' => ['required', 'in:active,implemented'],
        ]);

        $recommendation->update([
            'status' => $validated['status'],
        ]);

        $message = $validated['status'] === 'implemented'
            ? 'Great work! Marked this support activity as part of your home routine.'
            : 'Moved this support activity back to your active practice list.';

        return redirect()
            ->route('children.recommendations', $child)
            ->with('success', $message);
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
            'profile_picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_profile_picture' => ['nullable', 'boolean'],
        ]);

        unset($validated['profile_picture'], $validated['remove_profile_picture']);

        $validated['born_with_jaundice'] =
            $request->boolean('born_with_jaundice');

        $validated['family_member_with_asd'] =
            $request->boolean('family_member_with_asd');

        $validated['diagnosis_confirmed'] =
            $request->boolean('diagnosis_confirmed');

        if ($request->boolean('remove_profile_picture') && !empty($child->profile_picture)) {
            Storage::disk('public')->delete($child->profile_picture);
            $validated['profile_picture'] = null;
        }

        if ($request->hasFile('profile_picture')) {
            if (!empty($child->profile_picture)) {
                Storage::disk('public')->delete($child->profile_picture);
            }
            $validated['profile_picture'] = $request->file('profile_picture')->store('profile-pictures/children', 'public');
        }

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

        if (!empty($child->profile_picture)) {
            Storage::disk('public')->delete($child->profile_picture);
        }

        $child->delete();

        return redirect()
            ->route('children.index')
            ->with('success', 'Child profile deleted successfully.');
    }
}