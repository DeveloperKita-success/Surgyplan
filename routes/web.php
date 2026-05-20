<?php

use App\Http\Controllers\DashboardController as MainDashboardController;
use App\Http\Controllers\DoctorScheduleController as DoctorSurgeryScheduleController;
use App\Http\Controllers\GuidelineController as GuidelineFileController;
use App\Http\Controllers\NurseRegular\DashboardController as NurseRegularDashboardController;
use App\Http\Controllers\NurseRegular\PatientController as NurseRegularPatientController;
use App\Http\Controllers\NurseRegular\SurgeryRequestController as NurseRegularSurgeryRequestController;
use App\Http\Controllers\NurseUk\DashboardController as NurseUkDashboardController;
use App\Http\Controllers\NurseUk\DirectoryController as NurseUkDirectoryController;
use App\Http\Controllers\NurseUk\OperatingRoomController as NurseUkOperatingRoomController;
use App\Http\Controllers\NurseUk\OperationScheduleController as NurseUkOperationScheduleController;
use App\Http\Controllers\NurseUk\PatientController as NurseUkPatientController;
use App\Http\Controllers\NurseUk\SurgeryRequestController as NurseUkSurgeryRequestController;
use App\Http\Controllers\ProfileController as UserProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [MainDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/doctor', [MainDashboardController::class, 'doctor'])->middleware('doctor')->name('doctor');
        Route::get('/nurse-uk', [MainDashboardController::class, 'nurseUk'])->middleware('nurse-uk')->name('nurse.uk');
        Route::get('/nurse-regular', [MainDashboardController::class, 'nurseRegular'])->middleware('nurse-regular')->name('nurse.regular');
    });

    Route::prefix('doctor')->name('doctor.')->middleware('doctor')->group(function (): void {
        Route::get('/dashboard', [MainDashboardController::class, 'doctor'])->name('dashboard');
        Route::get('/surgery-schedules', [DoctorSurgeryScheduleController::class, 'index'])->name('schedules.index');
        Route::get('/surgery-schedules/{surgerySchedule}', [DoctorSurgeryScheduleController::class, 'show'])->name('schedules.show');
        Route::get('/surgery-reports', [DoctorSurgeryScheduleController::class, 'reports'])->name('reports.index');
    });

    Route::prefix('nurse-uk')->name('nurse-uk.')->middleware('nurse-uk')->group(function (): void {
        Route::get('/dashboard', [NurseUkDashboardController::class, 'index'])->name('dashboard');
        Route::resource('patients', NurseUkPatientController::class)->only(['index', 'show']);

        Route::get('/surgery-requests', [NurseUkSurgeryRequestController::class, 'index'])->name('requests.index');
        Route::get('/surgery-requests/{surgeryRequest}', [NurseUkSurgeryRequestController::class, 'show'])->name('requests.show');
        Route::post('/surgery-requests/{surgeryRequest}/decision', [NurseUkSurgeryRequestController::class, 'decide'])->name('requests.decide');
        Route::get('/surgery-schedules', [NurseUkOperationScheduleController::class, 'index'])->name('schedules.index');
        Route::resource('/operating-rooms', NurseUkOperatingRoomController::class)
            ->parameters(['operating-rooms' => 'operatingRoom'])
            ->names('rooms');
        Route::get('/doctors', [NurseUkDirectoryController::class, 'doctors'])->name('doctors.index');
    });

    Route::prefix('nurse-regular')->name('nurse-regular.')->middleware('nurse-regular')->group(function (): void {
        Route::get('/dashboard', [NurseRegularDashboardController::class, 'index'])->name('dashboard');
        Route::resource('patients', NurseRegularPatientController::class);

        Route::resource('/surgery-requests', NurseRegularSurgeryRequestController::class)
            ->parameters(['surgery-requests' => 'surgeryRequest'])
            ->names('surgery-requests');
    });

    Route::resource('guidelines', GuidelineFileController::class)->only(['index', 'store', 'destroy']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [UserProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [UserProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
