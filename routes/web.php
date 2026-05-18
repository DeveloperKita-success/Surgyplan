<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuidelineController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/dokter', [DashboardController::class, 'doctor'])->name('doctor');
        Route::get('/perawat-ok', [DashboardController::class, 'nurseUk'])->name('nurse.uk');
        Route::get('/perawat', [DashboardController::class, 'nurseRegular'])->name('nurse.regular');
    });

    Route::resource('patients', PatientController::class)->except(['create', 'store']);

    Route::resource('guidelines', GuidelineController::class)->only(['index', 'store', 'destroy']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
