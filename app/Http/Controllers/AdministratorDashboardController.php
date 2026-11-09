<?php

namespace App\Http\Controllers;

use App\Models\BehaviouralRecord;
use App\Models\Child;
use App\Models\Milestone;
use App\Models\Prediction;
use App\Models\Recommendation;
use App\Models\TherapyGoal;
use App\Models\User;
use App\Services\ChildPredictionService;
use Illuminate\View\View;

class AdministratorDashboardController extends Controller
{
    /**
     * Display the administrator dashboard with live platform and ML service oversight.
     */
    public function index(ChildPredictionService $predictionService): View
    {
        $practitioners = User::query()
            ->withCount(['children', 'assignedChildren'])
            ->latest()
            ->get();

        $totalUsersCount = $practitioners->count();
        $clinicianCount = $practitioners->where('role', 'clinician')->count();
        $caregiverCount = $practitioners->where('role', 'caregiver')->count();
        $adminCount = $practitioners->where('role', 'administrator')->count();

        $childrenCount = Child::count();
        $assignedChildrenCount = Child::whereHas('clinicians')->count();
        $unassignedChildrenCount = max(0, $childrenCount - $assignedChildrenCount);

        $observationsCount = BehaviouralRecord::count();
        $assessedObservationsCount = BehaviouralRecord::whereNotNull('ml_assessment_inputs')->count();

        $totalPredictionsCount = Prediction::count();
        $validatedPredictionsCount = Prediction::where('validated_by_clinician', true)->count();
        $pendingPredictionsCount = Prediction::where('validated_by_clinician', false)->count();

        $therapyGoalsCount = TherapyGoal::count();
        $milestonesCount = Milestone::count();
        $recommendationsCount = Recommendation::count();

        $mlHealth = $predictionService->checkHealth();

        return view('administrator.dashboard', compact(
            'practitioners',
            'totalUsersCount',
            'clinicianCount',
            'caregiverCount',
            'adminCount',
            'childrenCount',
            'assignedChildrenCount',
            'unassignedChildrenCount',
            'observationsCount',
            'assessedObservationsCount',
            'totalPredictionsCount',
            'validatedPredictionsCount',
            'pendingPredictionsCount',
            'therapyGoalsCount',
            'milestonesCount',
            'recommendationsCount',
            'mlHealth'
        ));
    }
}