<?php

use App\Http\Controllers\AdministratorDashboardController;
use App\Http\Controllers\CaregiverDashboardController;
use App\Http\Controllers\ClinicianDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'role:caregiver'])->group(function () {
    Route::get('/caregiver/dashboard', [CaregiverDashboardController::class, 'index'])
        ->name('caregiver.dashboard');
});

Route::middleware(['auth', 'role:clinician'])->group(function () {
    Route::get('/clinician/dashboard', [ClinicianDashboardController::class, 'index'])
        ->name('clinician.dashboard');
});

Route::middleware(['auth', 'role:administrator'])->group(function () {
    Route::get('/admin/dashboard', [AdministratorDashboardController::class, 'index'])
        ->name('administrator.dashboard');
});

require __DIR__.'/auth.php';