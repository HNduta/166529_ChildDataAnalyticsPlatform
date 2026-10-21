<?php

namespace App\Http\Controllers;

use App\Models\BehaviouralRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    private function authorizeAssignedClinician(
        Request $request,
        BehaviouralRecord $record
    ): void {
        abort_unless(
            $request->user()
                && $request->user()->role === 'clinician',
            403
        );

        $assigned = $record->child()
            ->whereHas('clinicians', function ($query) use ($request) {
                $query->where('users.id', $request->user()->id);
            })
            ->exists();

        abort_unless($assigned, 403);
    }

    public function edit(
        Request $request,
        BehaviouralRecord $behaviouralRecord
    ): View {
        $this->authorizeAssignedClinician(
            $request,
            $behaviouralRecord
        );

        return view('assessments.edit', [
            'record' => $behaviouralRecord->load('child'),
            'inputs' => $behaviouralRecord->ml_assessment_inputs ?? [],
        ]);
    }

    public function update(
        Request $request,
        BehaviouralRecord $behaviouralRecord
    ): RedirectResponse {
        $this->authorizeAssignedClinician(
            $request,
            $behaviouralRecord
        );

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

        $behaviouralRecord->update([
            'ml_assessment_inputs' => $validated,
        ]);

        return redirect()
            ->route('clinician.assessments.edit', $behaviouralRecord)
            ->with('status', 'Assessment inputs saved successfully.');
    }
}