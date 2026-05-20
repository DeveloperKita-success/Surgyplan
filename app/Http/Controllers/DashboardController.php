<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->role === User::ROLE_DOKTER) {
            return redirect()->route('doctor.dashboard');
        }

        if ($user->role === User::ROLE_PERAWAT_UK) {
            return redirect()->route('nurse-uk.dashboard');
        }

        return redirect()->route('nurse-regular.dashboard');
    }

    public function doctor(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless($user->role === User::ROLE_DOKTER, 403);

        return view('doctor.dashboard');
    }

    public function nurseUk(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless($user->role === User::ROLE_PERAWAT_UK, 403);

        return view('nurse-uk.dashboard');
    }

    public function nurseRegular(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless($user->role === User::ROLE_PERAWAT_BIASA, 403);

        return view('nurse-regular.dashboard');
    }
}
