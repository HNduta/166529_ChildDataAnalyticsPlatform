<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class CaregiverDashboardController extends Controller
{
    /**
     * Display the caregiver dashboard.
     */
    public function index(): View
    {
        return view('caregiver.dashboard');
    }
}