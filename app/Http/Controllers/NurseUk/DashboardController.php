<?php

namespace App\Http\Controllers\NurseUk;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();

        abort_unless($user->isUkNurse(), 403);

        return view('nurse-uk.dashboard');
    }
}
