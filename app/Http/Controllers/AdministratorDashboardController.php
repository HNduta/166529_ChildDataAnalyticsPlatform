<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\User;
use Illuminate\View\View;

class AdministratorDashboardController extends Controller
{
    /**
     * Display the administrator dashboard.
     */
    public function index(): View
    {
        $practitioners = User::query()
            ->latest()
            ->get();

        $totalUsersCount = $practitioners->count();
        $clinicianCount = $practitioners->where('role', 'clinician')->count();
        $caregiverCount = $practitioners->where('role', 'caregiver')->count();
        $childrenCount = Child::count();

        return view('administrator.dashboard', compact(
            'practitioners',
            'totalUsersCount',
            'clinicianCount',
            'caregiverCount',
            'childrenCount'
        ));
    }
}