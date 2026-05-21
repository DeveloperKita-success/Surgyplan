<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->to(match ($request->user()->role) {
                User::ROLE_DOKTER => route('doctor.dashboard', absolute: false),
                User::ROLE_PERAWAT_OK => route('nurse-ok.dashboard', absolute: false),
                User::ROLE_ADMIN => route('admin.dashboard', absolute: false),
                default => route('nurse-regular.dashboard', absolute: false),
            });
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
