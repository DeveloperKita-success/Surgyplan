<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorPatientController;
use App\Http\Controllers\DoctorScheduleController;
use App\Http\Controllers\GuidelineController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegularNursePatientController;
use App\Http\Controllers\RoomOperationRequestController;
use App\Http\Controllers\SurgeryRequestController;
use App\Http\Controllers\UkDirectoryController;
use App\Http\Controllers\UkSurgeryRequestController;
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

    Route::prefix('pengajuan-operasi-ruang')->name('nurse.regular.room-operation.')->group(function () {
        Route::get('/', [RoomOperationRequestController::class, 'create'])->name('create');
        Route::post('/', [RoomOperationRequestController::class, 'store'])->name('store');
    });

    Route::prefix('dokter')->name('doctor.')->group(function (): void {
        Route::get('/dashboard', [DashboardController::class, 'doctor'])->name('dashboard');
        Route::get('/jadwal-operasi', [DoctorScheduleController::class, 'index'])->name('schedules.index');
        Route::get('/jadwal-operasi/{surgerySchedule}', [DoctorScheduleController::class, 'show'])->name('schedules.show');
        Route::get('/laporan-operasi', [DoctorScheduleController::class, 'reports'])->name('reports.index');
        Route::get('/pasien', [DoctorPatientController::class, 'index'])->name('patients.index');
    });

    Route::prefix('perawat-uk')->name('uk.')->group(function (): void {
        Route::get('/dashboard', [DashboardController::class, 'nurseUk'])->name('dashboard');
        Route::get('/pengajuan-operasi', [UkSurgeryRequestController::class, 'index'])->name('requests.index');
        Route::get('/pengajuan-operasi/{surgeryRequest}', [UkSurgeryRequestController::class, 'show'])->name('requests.show');
        Route::post('/pengajuan-operasi/{surgeryRequest}/keputusan', [UkSurgeryRequestController::class, 'decide'])->name('requests.decide');
        Route::get('/pasien', [UkDirectoryController::class, 'patients'])->name('patients.index');
        Route::get('/jadwal-operasi', [UkDirectoryController::class, 'schedules'])->name('schedules.index');
        Route::get('/kamar-operasi', [UkDirectoryController::class, 'rooms'])->name('rooms.index');
        Route::get('/dokter', [UkDirectoryController::class, 'doctors'])->name('doctors.index');
    });

    Route::prefix('perawat')->name('nurse-regular.')->group(function (): void {
        Route::get('/dashboard', [DashboardController::class, 'nurseRegular'])->name('dashboard');
        Route::resource('/pengajuan-operasi', SurgeryRequestController::class)
            ->parameters(['pengajuan-operasi' => 'surgeryRequest'])
            ->names('surgery-requests');
        Route::get('/pasien', [RegularNursePatientController::class, 'index'])->name('patients.index');
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
