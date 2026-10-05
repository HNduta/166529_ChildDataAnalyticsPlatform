<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class CaregiverDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $children = $request->user()
            ->children()
            ->latest()
            ->get();

        return view('caregiver.dashboard', compact('children'));
    }
}