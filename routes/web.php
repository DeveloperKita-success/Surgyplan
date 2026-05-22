<?php

use App\Http\Controllers\AdminSpecialistController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardController as MainDashboardController;
use App\Http\Controllers\Doctor\DashboardController as DoctorDashboardController;
use App\Http\Controllers\DoctorScheduleController as DoctorSurgeryScheduleController;
use App\Http\Controllers\IcdApiController;
use App\Http\Controllers\NurseOk\DashboardController as NurseOkDashboardController;
use App\Http\Controllers\NurseOk\OperatingRoomController as NurseOkOperatingRoomController;
use App\Http\Controllers\NurseOk\OperationScheduleController as NurseOkOperationScheduleController;
use App\Http\Controllers\NurseOk\PatientController as NurseOkPatientController;
use App\Http\Controllers\NurseOk\SurgeryRequestController as NurseOkSurgeryRequestController;
use App\Http\Controllers\NurseRegular\DashboardController as NurseRegularDashboardController;
use App\Http\Controllers\NurseRegular\PatientController as NurseRegularPatientController;
use App\Http\Controllers\NurseRegular\SurgeryRequestController as NurseRegularSurgeryRequestController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProfileController as UserProfileController;
use App\Models\Patient;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $rekapPasien = [
        'igd' => Patient::query()
            ->where('origin_room', 'IGD')
            ->count(),
        'bangsal' => Patient::query()
            ->where('origin_room', 'Bangsal')
            ->count(),
        'poli' => Patient::query()
            ->where('origin_room', 'Poli')
            ->count(),
    ];

    return view('welcome', compact('rekapPasien'));
});

Route::get('/dashboard', [MainDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/doctor', [DoctorDashboardController::class, 'index'])->middleware('doctor')->name('doctor');
        Route::get('/nurse-ok', [MainDashboardController::class, 'nurseOk'])->middleware('nurse-ok')->name('nurse.ok');
        Route::get('/nurse-regular', [NurseRegularDashboardController::class, 'index'])->middleware('nurse-regular')->name('nurse.regular');
        Route::get('/admin', [DashboardController::class, 'admin'])->name('admin');
    });

    Route::prefix('doctor')->name('doctor.')->middleware('doctor')->group(function (): void {
        Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('dashboard');
        Route::get('/surgery-schedules', [DoctorSurgeryScheduleController::class, 'index'])->name('schedules.index');
        Route::get('/surgery-schedules/{surgerySchedule}', [DoctorSurgeryScheduleController::class, 'show'])->name('schedules.show');
    });

    Route::prefix('nurse-ok')->name('nurse-ok.')->middleware('nurse-ok')->group(function (): void {
        Route::get('/dashboard', [NurseOkDashboardController::class, 'index'])->name('dashboard');
        Route::resource('patients', NurseOkPatientController::class)->only(['index', 'show']);

        Route::get('/surgery-requests', [NurseOkSurgeryRequestController::class, 'index'])->name('requests.index');
        Route::get('/surgery-requests/{surgeryRequest}', [NurseOkSurgeryRequestController::class, 'show'])->name('requests.show');
        Route::post('/surgery-requests/{surgeryRequest}/decision', [NurseOkSurgeryRequestController::class, 'decide'])->name('requests.decide');
        Route::get('/surgery-schedules', [NurseOkOperationScheduleController::class, 'index'])->name('schedules.index');
        Route::get('/operating-rooms/patient-scheduling/create', [NurseOkOperatingRoomController::class, 'createPatientScheduling'])->name('rooms.patient-scheduling.create');
        Route::post('/operating-rooms/patient-scheduling', [NurseOkOperatingRoomController::class, 'storePatientScheduling'])->name('rooms.patient-scheduling.store');
        Route::resource('/operating-rooms', NurseOkOperatingRoomController::class)
            ->parameters(['operating-rooms' => 'operatingRoom'])
            ->names('rooms');
    });

    Route::prefix('nurse-regular')->name('nurse-regular.')->middleware('nurse-regular')->group(function (): void {
        Route::get('/dashboard', [NurseRegularDashboardController::class, 'index'])->name('dashboard');
        Route::resource('patients', NurseRegularPatientController::class);

        Route::resource('/surgery-requests', NurseRegularSurgeryRequestController::class)
            ->parameters(['surgery-requests' => 'surgeryRequest'])
            ->names('surgery-requests');
    });

    Route::prefix('admin')->name('admin.')->group(function (): void {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        Route::get('/online-count', [DashboardController::class, 'adminOnlineCount'])->name('online-count');
        Route::resource('/users', AdminUserController::class)->except(['index']);
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::resource('/specialists', AdminSpecialistController::class)->parameters(['specialists' => 'specialist']);
    });

    Route::resource('patients', PatientController::class)->middleware('nurse-regular');
    Route::get('/api/icd/search', [IcdApiController::class, 'search'])->name('api.icd.search');

    // Route::resource('guidelines', GuidelineController::class)->only(['index', 'store', 'destroy']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [UserProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [UserProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
