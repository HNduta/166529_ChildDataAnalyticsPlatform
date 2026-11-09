<?php

namespace App\Http\Controllers;

use App\Models\BehaviouralRecord;
use App\Models\Milestone;
use App\Models\Prediction;
use App\Models\Recommendation;
use App\Models\TherapyGoal;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CaregiverDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $caregiver = $request->user();

        $children = $caregiver
            ->children()
            ->with([
                'behaviouralRecords' => fn ($q) => $q->latest('observation_date')->latest('id'),
                'predictions' => fn ($q) => $q->latest('date_generated')->latest('id'),
                'recommendations' => fn ($q) => $q->latest('date_generated')->latest('id'),
                'therapyGoals.milestones',
            ])
            ->latest()
            ->get();

        $childIds = $children->pluck('id');

        $recentRecords = BehaviouralRecord::query()
            ->with('child')
            ->whereIn('child_id', $childIds)
            ->latest('observation_date')
            ->latest('id')
            ->take(8)
            ->get();

        $totalObservationsCount = BehaviouralRecord::query()
            ->whereIn('child_id', $childIds)
            ->count();

        $avgSuccessRate = $totalObservationsCount > 0
            ? round((float) BehaviouralRecord::whereIn('child_id', $childIds)->avg('success_rate'), 1)
            : null;

        $avgSessionMinutes = $totalObservationsCount > 0
            ? (int) round((float) BehaviouralRecord::whereIn('child_id', $childIds)->avg('session_duration_minutes'))
            : null;

        $latestPredictions = Prediction::query()
            ->with(['child', 'observation'])
            ->whereIn('child_id', $childIds)
            ->latest('date_generated')
            ->latest('id')
            ->take(4)
            ->get();

        $activeRecommendations = Recommendation::query()
            ->with('child')
            ->whereIn('child_id', $childIds)
            ->latest('date_generated')
            ->latest('id')
            ->take(5)
            ->get();

        $activeRecommendationsCount = Recommendation::query()
            ->whereIn('child_id', $childIds)
            ->whereIn('status', ['active', 'urgent', 'new'])
            ->count();

        $implementedRecommendationsCount = Recommendation::query()
            ->whereIn('child_id', $childIds)
            ->where('status', 'implemented')
            ->count();

        $therapyGoals = TherapyGoal::query()
            ->with(['child', 'milestones'])
            ->whereIn('child_id', $childIds)
            ->latest()
            ->get();

        $totalMilestonesCount = Milestone::query()
            ->whereIn('child_id', $childIds)
            ->count();

        $achievedMilestonesCount = Milestone::query()
            ->whereIn('child_id', $childIds)
            ->where('achieved_flag', true)
            ->count();

        return view('caregiver.dashboard', compact(
            'children',
            'recentRecords',
            'totalObservationsCount',
            'avgSuccessRate',
            'avgSessionMinutes',
            'latestPredictions',
            'activeRecommendations',
            'activeRecommendationsCount',
            'implementedRecommendationsCount',
            'therapyGoals',
            'totalMilestonesCount',
            'achievedMilestonesCount'
        ));
    }
}
