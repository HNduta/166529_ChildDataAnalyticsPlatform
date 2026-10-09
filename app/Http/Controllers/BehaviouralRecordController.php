<?php

namespace App\Http\Controllers;

use App\Models\BehaviouralRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BehaviouralRecordController extends Controller
{
    //Display behavioural records belonging to the authenticated caregiver.
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'child_id' => [
                'nullable',
                'integer',
            ],
            'observation_date' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],
        ]);

        $caregiver = $request->user();

        $query = $caregiver
            ->behaviouralRecords()
            ->with('child')
            ->latest('observation_date');

        // Filter by a child who belongs to this caregiver.
        if (!empty($filters['child_id'])) {
            $caregiver->children()->findOrFail($filters['child_id']);

            $query->where('child_id', $filters['child_id']);
        }

        // Filter by observation date.
        if (!empty($filters['observation_date'])) {
            $query->whereDate(
                'observation_date',
                $filters['observation_date']
            );
        }

        $records = $query->paginate(10)->withQueryString();

        $children = $caregiver
            ->children()
            ->orderBy('first_name')
            ->get();

        $caregiverRecords = $caregiver->behaviouralRecords();

        $totalRecords = (clone $caregiverRecords)->count();

        // Use the actual success_rate column for the success-rate metric.
        $averageSuccessRate = (clone $caregiverRecords)
            ->avg('success_rate');

        // Average session duration in minutes.
        $averageDuration = (clone $caregiverRecords)
            ->avg('session_duration_minutes');

        // Number of children with at least one behavioural observation.
        $childrenTracked = $caregiver
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

    //Shared validation rules for creating and updating observations.
     
    private function observationRules(): array
    {
        return [
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
                'between:1,5',
            ],

            'social_interaction_score' => [
                'required',
                'integer',
                'between:1,5',
            ],

            'engagement_level' => [
                'required',
                'integer',
                'between:1,5',
            ],

            'repetitive_behaviour_score' => [
                'required',
                'integer',
                'between:1,5',
            ],

            'session_duration_minutes' => [
                'nullable',
                'integer',
                'between:1,480',
            ],

            // Required because the database column is NOT NULL.
            'success_rate' => [
                'required',
                'numeric',
                'between:0,100',
            ],

            'prompts_required' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'eye_contact_rating' => [
                'nullable',
                'integer',
                'between:1,5',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }

    // Save a new behavioural observation.
     
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            $this->observationRules(),
            [
                'child_id.required' =>
                    'Please select a child for this observation.',

                'observation_date.required' =>
                    'Please enter the observation date.',

                'observation_date.before_or_equal' =>
                    'The observation date cannot be in the future.',

                'success_rate.required' =>
                    'Please enter the success rate.',

                'success_rate.between' =>
                    'Success rate must be between 0 and 100.',

                'prompts_required.integer' =>
                    'Prompts required must be a whole number.',

                'prompts_required.min' =>
                    'Prompts required cannot be negative.',
            ]
        );

        // Confirm that the selected child belongs to the current caregiver.
        $child = $request->user()
            ->children()
            ->findOrFail($validated['child_id']);

        $child->behaviouralRecords()->create([
            'caregiver_id' => $request->user()->id,
            'observation_date' => $validated['observation_date'],
            'communication_score' => $validated['communication_score'],
            'social_interaction_score' => $validated['social_interaction_score'],
            'engagement_level' => $validated['engagement_level'],
            'repetitive_behaviour_score' => $validated['repetitive_behaviour_score'],
            'session_duration_minutes' =>
                $validated['session_duration_minutes'] ?? null,
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

     // Show the existing edit form for an observation owned by the caregiver.

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

    //Update an existing behavioural observation.
    
    public function update(
        Request $request,
        BehaviouralRecord $behaviouralRecord
    ): RedirectResponse {
        // Ensure the record belongs to the authenticated caregiver.
        $record = $request->user()
            ->behaviouralRecords()
            ->findOrFail($behaviouralRecord->id);

        $validated = $request->validate(
            $this->observationRules(),
            [
                'child_id.required' =>
                    'Please select a child for this observation.',

                'observation_date.required' =>
                    'Please enter the observation date.',

                'observation_date.before_or_equal' =>
                    'The observation date cannot be in the future.',

                'success_rate.required' =>
                    'Please enter the success rate.',

                'success_rate.between' =>
                    'Success rate must be between 0 and 100.',

                'prompts_required.integer' =>
                    'Prompts required must be a whole number.',

                'prompts_required.min' =>
                    'Prompts required cannot be negative.',
            ]
        );

        // The new child must also belong to the same caregiver.
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
            'session_duration_minutes' =>
                $validated['session_duration_minutes'] ?? null,
            'success_rate' => $validated['success_rate'],
            'prompts_required' => $validated['prompts_required'] ?? null,
            'eye_contact_rating' => $validated['eye_contact_rating'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('behavioural-records.index')
            ->with(
                'success',
                'Behavioural observation updated successfully.'
            );
    }

    // Delete an observation belonging to the authenticated caregiver.
    
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