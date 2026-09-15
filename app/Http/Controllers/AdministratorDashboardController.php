<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AdministratorDashboardController extends Controller
{
    /**
     * Display the administrator dashboard.
     */
    public function index(): View
    {
        return view('administrator.dashboard');
    }
}