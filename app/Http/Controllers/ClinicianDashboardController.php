<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ClinicianDashboardController extends Controller
{
    /**
     * Display the clinician dashboard.
     */
    public function index(): View
    {
        return view('clinician.dashboard');
    }
}