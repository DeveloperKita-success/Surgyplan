<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->to(match ($request->user()->role) {
                User::ROLE_DOKTER => route('doctor.dashboard', absolute: false),
                User::ROLE_PERAWAT_UK => route('uk.dashboard', absolute: false),
                User::ROLE_ADMIN => route('admin.dashboard', absolute: false),
                default => route('nurse-regular.dashboard', absolute: false),
            });
        }

        return view('auth.verify-email');
    }
}
