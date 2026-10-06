<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BehaviouralRecordController extends Controller
{
    public function create(Request $request): View
    {
        $children = $request->user()
            ->children()
            ->orderBy('first_name')
            ->get();

        return view('caregiver.behavioural-records.create', compact('children'));
    }

    /**
     * Store a new behavioural observation.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // Required — core identifiers
            'child_id' => ['required', 'integer', 'exists:children,id'],
            'observation_date' => ['required', 'date', 'before_or_equal:today'],

            // Required — the one metric the deployed model actually consumes
            'success_rate' => ['required', 'numeric', 'min:0', 'max:100'],

            // Optional — collected now for future feature-stability testing
            'engagement_level' => ['nullable', 'integer', 'min:1', 'max:5'],
            'prompts_required' => ['nullable', 'numeric', 'min:0'],
            'eye_contact_rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'session_duration_minutes' => ['nullable', 'integer', 'min:1', 'max:480'],

            // Optional — domain scores kept for dashboards only, not model input
            'communication_score' => ['nullable', 'integer', 'min:1', 'max:5'],
            'social_interaction_score' => ['nullable', 'integer', 'min:1', 'max:5'],
            'repetitive_behaviour_score' => ['nullable', 'integer', 'min:1', 'max:5'],

            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        // Confirm the selected child actually belongs to the authenticated
        // caregiver — never trust the submitted child_id on its own.
        $child = $request->user()
            ->children()
            ->findOrFail($validated['child_id']);

        $child->behaviouralRecords()->create([
            // Associate the observation with whoever is actually submitting
            // it rather than trusting any value a client could send.
            'caregiver_id' => $request->user()->id,

            'observation_date' => $validated['observation_date'],
            'success_rate' => $validated['success_rate'],

            'engagement_level' => $validated['engagement_level'] ?? null,
            'prompts_required' => $validated['prompts_required'] ?? null,
            'eye_contact_rating' => $validated['eye_contact_rating'] ?? null,
            'session_duration_minutes' => $validated['session_duration_minutes'] ?? null,

            'communication_score' => $validated['communication_score'] ?? null,
            'social_interaction_score' => $validated['social_interaction_score'] ?? null,
            'repetitive_behaviour_score' => $validated['repetitive_behaviour_score'] ?? null,

            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('children.show', $child)
            ->with('success', 'Behavioural observation recorded successfully.');
    }
}