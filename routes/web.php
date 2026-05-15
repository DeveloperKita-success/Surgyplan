<?php

use App\Http\Controllers\ProfileController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    /** @var \App\Models\User $user */
    $user = auth()->user();
    $user->loadMissing(['doctor', 'nurse']);

    if ($user->role === User::ROLE_DOKTER) {
        return redirect()->route('dashboard.doctor');
    }

    if ($user->role === User::ROLE_PERAWAT && optional($user->nurse)->type === 'ok') {
        return redirect()->route('dashboard.nurse.ok');
    }

    return redirect()->route('dashboard.nurse.regular');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard/dokter', function () {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $user->loadMissing('doctor');

        abort_unless($user->role === User::ROLE_DOKTER, 403);

        return view('dashboard.doctor');
    })->name('dashboard.doctor');

    Route::get('/dashboard/perawat-ok', function () {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $user->loadMissing('nurse');

        abort_unless($user->role === User::ROLE_PERAWAT && optional($user->nurse)->type === 'ok', 403);

        return view('dashboard.nurse-ok');
    })->name('dashboard.nurse.ok');

    Route::get('/dashboard/perawat', function () {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $user->loadMissing('nurse');

        abort_unless($user->role === User::ROLE_PERAWAT && optional($user->nurse)->type === 'biasa', 403);

        return view('dashboard.nurse-regular');
    })->name('dashboard.nurse.regular');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
