<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use App\Models\User;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->to($this->redirectToRoleDashboard($request).'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->to($this->redirectToRoleDashboard($request).'?verified=1');
    }

    private function redirectToRoleDashboard(EmailVerificationRequest $request): string
    {
        return match ($request->user()->role) {
            User::ROLE_DOKTER => route('doctor.dashboard', absolute: false),
            User::ROLE_PERAWAT_OK => route('nurse-ok.dashboard', absolute: false),
            User::ROLE_ADMIN => route('admin.dashboard', absolute: false),
            default => route('nurse-regular.dashboard', absolute: false),
        };
    }
}
