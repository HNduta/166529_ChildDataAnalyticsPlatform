<?php

namespace App\Http\Controllers;

use App\Models\BehaviouralRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BehaviouralRecordController extends Controller
{
    /**
     * Display behavioural records belonging to the authenticated caregiver.
     */
    public function index(Request $request): View
    {
        $query = $request->user()
            ->behaviouralRecords()
            ->with('child')
            ->latest('observation_date');

        // Filter by child
        if ($request->filled('child_id')) {
            $childId = $request->input('child_id');

            // Make sure the selected child belongs to the caregiver
            $request->user()
                ->children()
                ->findOrFail($childId);

            $query->where('child_id', $childId);
        }

        // Filter by observation date
        if ($request->filled('observation_date')) {
            $query->whereDate(
                'observation_date',
                $request->input('observation_date')
            );
        }

        $records = $query->paginate(10)->withQueryString();

        // All children belonging to the caregiver
        $children = $request->user()
            ->children()
            ->orderBy('first_name')
            ->get();

        // Total number of records
        $totalRecords = $request->user()
            ->behaviouralRecords()
            ->count();

        // Average score across all four behavioural indicators
        $averageScore = $request->user()
            ->behaviouralRecords()
            ->selectRaw(
                'AVG(
                    (
                        communication_score +
                        social_interaction_score +
                        engagement_level +
                        repetitive_behaviour_score
                    ) / 4.0
                ) as average_score'
            )
            ->value('average_score');

        $averageSuccessRate = $averageScore !== null
            ? ($averageScore / 5) * 100
            : null;

        // Average session duration
        $averageDuration = $request->user()
            ->behaviouralRecords()
            ->avg('session_duration_minutes');

        // Number of children with at least one observation
        $childrenTracked = $request->user()
            ->children()
            ->whereHas('behaviouralRecords')
            ->count();

        return view(
            'caregiver.behavioural-records.index',
            compact(
                'records',
                'children',
                'totalRecords',
                'averageSuccessRate',
                'averageDuration',
                'childrenTracked'
            )
        );
    }

    /**
     * Show the observation form.
     */
    public function create(Request $request): View
    {
        $children = $request->user()
            ->children()
            ->orderBy('first_name')
            ->get();

        return view(
            'caregiver.behavioural-records.create',
            compact('children')
        );
    }

    /**
     * Store a new behavioural observation.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'child_id' => [
                'required',
                'integer',
                'exists:children,id',
            ],

            'observation_date' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            'communication_score' => [
                'required',
                'integer',
                'min:1',
                'max:5',
            ],

            'social_interaction_score' => [
                'required',
                'integer',
                'min:1',
                'max:5',
            ],

            'engagement_level' => [
                'required',
                'integer',
                'min:1',
                'max:5',
            ],

            'repetitive_behaviour_score' => [
                'required',
                'integer',
                'min:1',
                'max:5',
            ],

            'session_duration_minutes' => [
                'nullable',
                'integer',
                'min:1',
                'max:480',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'success_rate' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],

            'prompts_required' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'eye_contact_rating' => [
                'nullable',
                'integer',
                'min:1',
                'max:5',
            ],
        ]);

        // Make sure the selected child belongs to this caregiver.
        $child = $request->user()
            ->children()
            ->findOrFail($validated['child_id']);

        // Assign caregiver from the authenticated user.
        $validated['caregiver_id'] = $request->user()->id;

        $child->behaviouralRecords()->create([
           'caregiver_id' => $request->user()->id,
            'observation_date' => $validated['observation_date'],
            'communication_score' => $validated['communication_score'],
            'social_interaction_score' => $validated['social_interaction_score'],
            'engagement_level' => $validated['engagement_level'],
            'repetitive_behaviour_score' => $validated['repetitive_behaviour_score'],
            'session_duration_minutes' => $validated['session_duration_minutes'] ?? null,
            'success_rate' => $validated['success_rate'],
            'prompts_required' => $validated['prompts_required'] ?? null,
            'eye_contact_rating' => $validated['eye_contact_rating'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('behavioural-records.index')
            ->with(
                'success',
                'Behavioural observation recorded successfully.'
            );
    }

    /**
     * Show the edit form.
     *
     * The caregiver uses the same Observation Form design.
     */
    public function edit(
        Request $request,
        BehaviouralRecord $behaviouralRecord
    ): View {
        $record = $request->user()
            ->behaviouralRecords()
            ->with('child')
            ->findOrFail($behaviouralRecord->id);

        $children = $request->user()
            ->children()
            ->orderBy('first_name')
            ->get();

        return view(
            'caregiver.behavioural-records.edit',
            compact('record', 'children')
        );
    }

    /**
     * Update an existing behavioural observation.
     */
    public function update(
        Request $request,
        BehaviouralRecord $behaviouralRecord
    ): RedirectResponse {
        // Make sure the record belongs to the authenticated caregiver.
        $record = $request->user()
            ->behaviouralRecords()
            ->findOrFail($behaviouralRecord->id);

        $validated = $request->validate([
            'child_id' => [
                'required',
                'integer',
                'exists:children,id',
            ],

            'observation_date' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            'communication_score' => [
                'required',
                'integer',
                'min:1',
                'max:5',
            ],

            'social_interaction_score' => [
                'required',
                'integer',
                'min:1',
                'max:5',
            ],

            'engagement_level' => [
                'required',
                'integer',
                'min:1',
                'max:5',
            ],

            'repetitive_behaviour_score' => [
                'required',
                'integer',
                'min:1',
                'max:5',
            ],

            'session_duration_minutes' => [
                'nullable',
                'integer',
                'min:1',
                'max:480',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'success_rate' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],

            'prompts_required' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'eye_contact_rating' => [
                'nullable',
                'integer',
                'min:1',
                'max:5',
            ],
        ]);

        // Make sure the selected child belongs to this caregiver.
        $child = $request->user()
            ->children()
            ->findOrFail($validated['child_id']);

        $record->update([
            'child_id' => $child->id,
            'observation_date' => $validated['observation_date'],
            'communication_score' => $validated['communication_score'],
            'social_interaction_score' => $validated['social_interaction_score'],
            'engagement_level' => $validated['engagement_level'],
            'repetitive_behaviour_score' => $validated['repetitive_behaviour_score'],
            'session_duration_minutes' => $validated['session_duration_minutes'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'success_rate' => $validated['success_rate'],
            'prompts_required' => $validated['prompts_required'] ?? null,
            'eye_contact_rating' => $validated['eye_contact_rating'] ?? null,
        ]);

        return redirect()
            ->route('behavioural-records.index')
            ->with(
                'success',
                'Behavioural observation updated successfully.'
            );
    }

    /**
     * Delete an existing behavioural observation.
     */
    public function destroy(
        Request $request,
        BehaviouralRecord $behaviouralRecord
    ): RedirectResponse {
        $record = $request->user()
            ->behaviouralRecords()
            ->findOrFail($behaviouralRecord->id);

        $record->delete();

        return redirect()
            ->route('behavioural-records.index')
            ->with(
                'success',
                'Behavioural observation deleted successfully.'
            );
    }
}