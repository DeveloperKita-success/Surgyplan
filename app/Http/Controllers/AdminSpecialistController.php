<?php

namespace App\Http\Controllers;

use App\Models\Specialist;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminSpecialistController extends Controller
{
    private function ensureAdminAccess(): void
    {
        /** @var User $user */
        $user = Auth::user();

        abort_unless($user->role === User::ROLE_ADMIN, 403);
    }

    public function index(): View
    {
        $this->ensureAdminAccess();

        $specialists = Specialist::query()
            ->orderBy('name')
            ->get(['id', 'name', 'description', 'created_at']);

        return view('admin.specialists.index', [
            'specialists' => $specialists,
            'summary' => [
                'total' => $specialists->count(),
            ],
        ]);
    }

    public function create(): View
    {
        $this->ensureAdminAccess();

        return view('admin.specialists.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureAdminAccess();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        Specialist::create($validated);

        return redirect()
            ->route('admin.specialists.index')
            ->with('status', 'Data spesialis berhasil ditambahkan.');
    }

    public function show(Specialist $specialist): View
    {
        $this->ensureAdminAccess();

        return view('admin.specialists.show', [
            'specialist' => $specialist->loadCount(['doctors', 'operatingRooms']),
        ]);
    }

    public function edit(Specialist $specialist): View
    {
        $this->ensureAdminAccess();

        return view('admin.specialists.edit', [
            'specialist' => $specialist,
        ]);
    }

    public function update(Request $request, Specialist $specialist): RedirectResponse
    {
        $this->ensureAdminAccess();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $specialist->update($validated);

        return redirect()
            ->route('admin.specialists.show', $specialist)
            ->with('status', 'Data spesialis berhasil diperbarui.');
    }

    public function destroy(Specialist $specialist): RedirectResponse
    {
        $this->ensureAdminAccess();

        if ($specialist->doctors()->exists() || $specialist->operatingRooms()->exists()) {
            return redirect()
                ->route('admin.specialists.index')
                ->with('status', 'Spesialis tidak bisa dihapus karena masih dipakai.');
        }

        $specialist->delete();

        return redirect()
            ->route('admin.specialists.index')
            ->with('status', 'Data spesialis berhasil dihapus.');
    }
}
