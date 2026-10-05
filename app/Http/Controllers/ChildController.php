<?php

namespace App\Http\Controllers;

use App\Models\Child;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChildController extends Controller
{
    /**
     * Display the authenticated caregiver's children.
     */
    public function index(Request $request): View
    {
        $children = $request->user()
            ->children()
            ->latest()
            ->paginate(10);

        return view('caregiver.children.index', compact('children'));
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
        $child = $request->user()
            ->children()
            ->findOrFail($child->id);

        return view('caregiver.children.show', compact('child'));
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