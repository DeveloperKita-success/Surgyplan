<?php

namespace App\Http\Controllers;

use App\Models\Guideline;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GuidelineController extends Controller
{
    private function abortUnlessAllowedRole(): void
    {
        /** @var User|null $user */
        $user = auth()->user();

        abort_unless($user && in_array($user->role, [User::ROLE_DOKTER, User::ROLE_PERAWAT_BIASA, User::ROLE_PERAWAT_UK], true), 403);
    }

    public function index(Request $request): View
    {
        $this->abortUnlessAllowedRole();

        $query = $request->string('q')->trim()->toString();
        $type = $request->string('type')->trim()->toString();

        $guidelines = Guideline::query()
            ->with('uploadedBy')
            ->when($query !== '', fn ($builder) => $builder->where('title', 'like', "%{$query}%"))
            ->when($type !== '', fn ($builder) => $builder->where('type', $type))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $typeOptions = Guideline::query()
            ->select('type')
            ->distinct()
            ->orderBy('type')
            ->pluck('type')
            ->values();

        return view('guidelines.index', [
            'guidelines' => $guidelines,
            'query' => $query,
            'type' => $type,
            'typeOptions' => $typeOptions,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->abortUnlessAllowedRole();

        /** @var User $user */
        $user = auth()->user();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('guidelines', 'public');
        }

        Guideline::query()->create([
            'title' => $data['title'],
            'type' => $data['type'],
            'description' => $data['description'] ?? null,
            'file' => $filePath,
            'uploaded_by' => $user->id,
        ]);

        return redirect()
            ->route('guidelines.index')
            ->with('status', 'Buku pedoman berhasil ditambahkan.');
    }

    public function destroy(Guideline $guideline): RedirectResponse
    {
        $this->abortUnlessAllowedRole();

        /** @var User $user */
        $user = auth()->user();

        abort_unless($user->isDoctor() || $guideline->uploaded_by === $user->id, 403);

        if ($guideline->file) {
            Storage::disk('public')->delete($guideline->file);
        }

        $guideline->delete();

        return redirect()
            ->route('guidelines.index')
            ->with('status', 'Buku pedoman berhasil dihapus.');
    }
}
