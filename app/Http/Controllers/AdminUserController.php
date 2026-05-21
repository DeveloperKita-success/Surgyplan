<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Nurse;
use App\Models\Specialist;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * @return array<int, string>
     */
    private function allowedRoles(): array
    {
        return [
            User::ROLE_DOKTER,
            User::ROLE_PERAWAT_OK,
            User::ROLE_PERAWAT_BIASA,
        ];
    }

    /**
     * @return array<int, string>
     */
    private function createRoles(): array
    {
        return [
            User::ROLE_DOKTER,
            'perawat',
        ];
    }

    private function ensureAdminAccess(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        abort_unless($user->role === User::ROLE_ADMIN, 403);
    }

    public function index(): View
    {
        $this->ensureAdminAccess();

        $cutoff = now()->subMinute()->timestamp;
        $onlineUserIds = DB::table('sessions')
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', $cutoff)
            ->distinct()
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $users = User::query()
            ->where('role', '!=', User::ROLE_ADMIN)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role', 'created_at'])
            ->map(function (User $userItem) use ($onlineUserIds) {
                $roleLabel = match ($userItem->role) {
                    User::ROLE_DOKTER => 'Dokter',
                    User::ROLE_PERAWAT_OK => 'Perawat OK',
                    User::ROLE_PERAWAT_BIASA => 'Perawat Reguler',
                    User::ROLE_ADMIN => 'Admin',
                    default => 'Pengguna',
                };

                return [
                    'id' => $userItem->id,
                    'name' => $userItem->name,
                    'email' => $userItem->email,
                    'role' => $roleLabel,
                    'created_at' => $userItem->created_at?->format('d M Y') ?? '-',
                    'is_online' => in_array($userItem->id, $onlineUserIds, true),
                ];
            });

        $summary = [
            'total' => $users->count(),
            'doctor' => $users->where('role', 'Dokter')->count(),
            'nurse_ok' => $users->where('role', 'Perawat OK')->count(),
            'nurse_regular' => $users->where('role', 'Perawat Reguler')->count(),
        ];

        return view('admin.users.index', [
            'users' => $users,
            'summary' => $summary,
        ]);
    }

    public function create(): View
    {
        $this->ensureAdminAccess();

        return view('admin.users.create', [
            'roles' => $this->createRoles(),
            'specialists' => Specialist::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureAdminAccess();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in($this->createRoles())],
            'password' => ['required', 'string', 'min:8'],
            'specialist_id' => [Rule::requiredIf($request->role === User::ROLE_DOKTER), 'nullable', 'exists:specialists,id'],
            'str_number' => [Rule::requiredIf($request->role === User::ROLE_DOKTER), 'nullable', 'string', 'max:255'],
            'title' => [Rule::requiredIf($request->role === User::ROLE_DOKTER), 'nullable', 'string', 'max:255'],
            'sip_number' => [Rule::requiredIf($request->role === User::ROLE_DOKTER), 'nullable', 'string', 'max:255'],
            'nurse_is_ok' => [Rule::requiredIf($request->role === 'perawat'), 'nullable', 'boolean'],
            'origin_unit' => [
                Rule::requiredIf(
                    $request->role === 'perawat' && !$request->boolean('nurse_is_ok')
                ),
                'nullable',
                Rule::in(['Bangsal', 'IGD', 'Poli']),
            ],
        ]);

        $roleToStore = $validated['role'] === User::ROLE_DOKTER
            ? User::ROLE_DOKTER
            : ($request->boolean('nurse_is_ok') ? User::ROLE_PERAWAT_OK : User::ROLE_PERAWAT_BIASA);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $roleToStore,
            'password' => Hash::make($validated['password']),
        ]);

        if ($user && $roleToStore === User::ROLE_DOKTER) {
            Doctor::create([
                'user_id' => $user->id,
                'specialist_id' => $validated['specialist_id'],
                'title' => $validated['title'],
                'str_number' => $validated['str_number'],
                'sip_number' => $validated['sip_number'],
            ]);
        }

        if ($user && in_array($roleToStore, [User::ROLE_PERAWAT_OK, User::ROLE_PERAWAT_BIASA], true)) {
            Nurse::create([
                'user_id' => $user->id,
                'nurse_type' => $roleToStore,
                'origin_unit' => $roleToStore === User::ROLE_PERAWAT_BIASA ? $validated['origin_unit'] : null,
            ]);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User berhasil ditambahkan.');
    }

    public function show(User $user): View
    {
        $this->ensureAdminAccess();

        abort_unless($user->role !== User::ROLE_ADMIN, 404);

        $user->load(['doctor.specialist', 'nurse']);

        return view('admin.users.show', [
            'userItem' => $user,
        ]);
    }

    public function edit(User $user): View
    {
        $this->ensureAdminAccess();

        abort_unless($user->role !== User::ROLE_ADMIN, 404);

        $user->load(['doctor', 'nurse']);

        return view('admin.users.edit', [
            'userItem' => $user,
            'roles' => $this->allowedRoles(),
            'specialists' => Specialist::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->ensureAdminAccess();

        abort_unless($user->role !== User::ROLE_ADMIN, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in($this->allowedRoles())],
            'password' => ['nullable', 'string', 'min:8'],
            'specialist_id' => [Rule::requiredIf($request->role === User::ROLE_DOKTER), 'nullable', 'exists:specialists,id'],
            'str_number' => [Rule::requiredIf($request->role === User::ROLE_DOKTER), 'nullable', 'string', 'max:255'],
            'title' => [Rule::requiredIf($request->role === User::ROLE_DOKTER), 'nullable', 'string', 'max:255'],
            'sip_number' => [Rule::requiredIf($request->role === User::ROLE_DOKTER), 'nullable', 'string', 'max:255'],
            'origin_unit' => [
                Rule::requiredIf($request->role === User::ROLE_PERAWAT_BIASA),
                'nullable',
                Rule::in(['Bangsal', 'IGD', 'Poli']),
            ],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if ($validated['role'] === User::ROLE_DOKTER) {
            $doctor = Doctor::firstOrNew(['user_id' => $user->id]);
            $doctor->specialist_id = $validated['specialist_id'];
            $doctor->title = $validated['title'];
            $doctor->str_number = $validated['str_number'];
            $doctor->sip_number = $validated['sip_number'];
            $doctor->save();

            Nurse::where('user_id', $user->id)->delete();
        }

        if (in_array($validated['role'], [User::ROLE_PERAWAT_OK, User::ROLE_PERAWAT_BIASA], true)) {
            $nurse = Nurse::firstOrNew(['user_id' => $user->id]);
            $nurse->nurse_type = $validated['role'];
            $nurse->origin_unit = $validated['role'] === User::ROLE_PERAWAT_BIASA ? $validated['origin_unit'] : null;
            $nurse->save();

            Doctor::where('user_id', $user->id)->delete();
        }

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->ensureAdminAccess();

        abort_unless($user->role !== User::ROLE_ADMIN, 404);

        if ($user->doctor && $user->doctor->surgerySchedules()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'Dokter memiliki jadwal operasi, tidak bisa dihapus.');
        }

        if ($user->doctor && $user->doctor->operationReports()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'Dokter memiliki laporan operasi, tidak bisa dihapus.');
        }

        if ($user->surgeryRequests()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'User memiliki pengajuan operasi, tidak bisa dihapus.');
        }

        if ($user->createdPatients()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'User memiliki data pasien, tidak bisa dihapus.');
        }

        if ($user->surgerySchedulesApproved()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'User memiliki jadwal operasi yang disetujui, tidak bisa dihapus.');
        }

        DB::transaction(function () use ($user): void {
            $user->notifications()->delete();
            $user->surgeryHistories()->delete();
            $user->okVerificationChecklists()->delete();
            $user->guidelines()->delete();

            $user->doctor()?->delete();
            $user->nurse()?->delete();

            $user->delete();
        });

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User berhasil dihapus.');
    }
}
