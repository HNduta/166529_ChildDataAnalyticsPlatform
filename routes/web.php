<?php

use App\Http\Controllers\AdministratorDashboardController;
use App\Http\Controllers\CaregiverDashboardController;
use App\Http\Controllers\ClinicianDashboardController;
use App\Http\Controllers\ChildController;
use App\Http\Controllers\BehaviouralRecordController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'role:caregiver'])->group(function () {

    Route::get('/caregiver/dashboard', [CaregiverDashboardController::class, 'index'])
        ->name('caregiver.dashboard');

    Route::resource('children', ChildController::class);

    Route::get('/behavioural-records/create', [BehaviouralRecordController::class, 'create'])
        ->name('behavioural-records.create');

    Route::post('/behavioural-records', [BehaviouralRecordController::class, 'store'])
        ->name('behavioural-records.store');

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