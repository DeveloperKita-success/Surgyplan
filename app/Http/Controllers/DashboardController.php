<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        if ($user->role === User::ROLE_PERAWAT_OK) {
            return redirect()->route('nurse-ok.dashboard');
        }

        if ($user->role === User::ROLE_ADMIN) {
            return redirect()->route('admin.dashboard');
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

    public function nurseOk(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless($user->role === User::ROLE_PERAWAT_OK, 403);

        return view('nurse-ok.dashboard');
    }

    public function nurseRegular(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless($user->role === User::ROLE_PERAWAT_BIASA, 403);

        return view('nurse-regular.dashboard');
    }

    public function admin(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless($user->role === User::ROLE_ADMIN, 403);

        $latestSessions = DB::table('sessions')
            ->select('user_id', DB::raw('MAX(last_activity) as last_activity'))
            ->whereNotNull('user_id')
            ->groupBy('user_id');

        $recentActiveUsers = DB::query()
            ->fromSub($latestSessions, 'latest_sessions')
            ->join('users', 'users.id', '=', 'latest_sessions.user_id')
            ->where('users.role', '!=', User::ROLE_ADMIN)
            ->orderByDesc('latest_sessions.last_activity')
            ->limit(6)
            ->get(['users.name', 'users.role', 'latest_sessions.last_activity'])
            ->map(function ($row) {
                $roleLabel = match ($row->role) {
                    User::ROLE_DOKTER => 'Dokter',
                    User::ROLE_PERAWAT_OK => 'Perawat OK',
                    User::ROLE_PERAWAT_BIASA => 'Perawat Reguler',
                    default => 'Pengguna',
                };

                return [
                    'name' => $row->name,
                    'role' => $roleLabel,
                    'last_active' => Carbon::createFromTimestamp($row->last_activity)->diffForHumans(),
                ];
            });

        $defaultDbConnection = config('database.default');
        $dbDriver = config("database.connections.{$defaultDbConnection}.driver");
        $dbName = config("database.connections.{$defaultDbConnection}.database");

        $systemStatus = [
            ['label' => 'Framework', 'value' => app()->version()],
            ['label' => 'PHP', 'value' => PHP_VERSION],
            ['label' => 'Database', 'value' => strtoupper((string) $dbDriver)],
            ['label' => 'DB Name', 'value' => $dbName ?: '-'],
        ];

        return view('dashboard.admin', [
            'recentActiveUsers' => $recentActiveUsers,
            'systemStatus' => $systemStatus,
        ]);
    }

    public function adminOnlineCount(): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless($user->role === User::ROLE_ADMIN, 403);

        $cutoff = now()->subMinute()->timestamp;

        $onlineCount = DB::table('sessions')
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', $cutoff)
            ->distinct('user_id')
            ->count('user_id');

        $roleCounts = DB::table('sessions')
            ->join('users', 'users.id', '=', 'sessions.user_id')
            ->whereNotNull('sessions.user_id')
            ->where('sessions.last_activity', '>=', $cutoff)
            ->groupBy('users.role')
            ->select('users.role', DB::raw('COUNT(DISTINCT sessions.user_id) as total'))
            ->pluck('total', 'role');

        return response()->json([
            'online' => $onlineCount,
            'online_doctor' => (int) ($roleCounts[User::ROLE_DOKTER] ?? 0),
            'online_ok_nurse' => (int) ($roleCounts[User::ROLE_PERAWAT_OK] ?? 0),
            'online_regular_nurse' => (int) ($roleCounts[User::ROLE_PERAWAT_BIASA] ?? 0),
            'cutoff_seconds' => 60,
        ]);
    }
}
