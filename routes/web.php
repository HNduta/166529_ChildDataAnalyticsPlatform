<?php

use App\Http\Controllers\AdministratorDashboardController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\BehaviouralRecordController;
use App\Http\Controllers\CaregiverDashboardController;
use App\Http\Controllers\ChildController;
use App\Http\Controllers\ClinicianDashboardController;
use App\Http\Controllers\PredictionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'role:caregiver'])->group(function () {

    Route::get('/caregiver/dashboard', [CaregiverDashboardController::class, 'index'])
        ->name('caregiver.dashboard');

    Route::resource('children', ChildController::class);

    Route::resource('behavioural-records', BehaviouralRecordController::class)
        ->except(['show']);

    Route::get(
        '/predictions',
        [PredictionController::class, 'caregiverIndex']
    )->name('predictions.index');
});

Route::middleware(['auth', 'role:clinician'])->group(function () {

    Route::get('/clinician/dashboard', [ClinicianDashboardController::class, 'index'])
        ->name('clinician.dashboard');

    Route::post(
        '/behavioural-records/{behaviouralRecord}/predict',
        [PredictionController::class, 'generate']
    )->name('behavioural-records.predict');

    Route::get(
        '/clinician/predictions',
        [PredictionController::class, 'clinicianIndex']
    )->name('clinician.predictions.index');

    Route::get(
        '/clinician/observations/{behaviouralRecord}/assessment',
        [AssessmentController::class, 'edit']
    )->name('clinician.assessments.edit');

    Route::put(
        '/clinician/observations/{behaviouralRecord}/assessment',
        [AssessmentController::class, 'update']
    )->name('clinician.assessments.update');
});

Route::middleware(['auth', 'role:administrator'])->group(function () {

    Route::get('/admin/dashboard', [AdministratorDashboardController::class, 'index'])
        ->name('administrator.dashboard');
});

require __DIR__.'/auth.php';