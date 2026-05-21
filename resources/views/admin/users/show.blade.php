<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Kontrol akses dan akun</p>
            <h1 class="text-2xl font-bold text-slate-900">Detail User</h1>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6">
        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Nama</p>
                    <p class="mt-2 text-sm font-semibold text-slate-900">{{ $userItem->name }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Email</p>
                    <p class="mt-2 text-sm text-slate-700">{{ $userItem->email }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Role</p>
                    <p class="mt-2 text-sm text-slate-700">
                        {{ match ($userItem->role) {
                            \App\Models\User::ROLE_DOKTER => 'Dokter',
                            \App\Models\User::ROLE_PERAWAT_OK => 'Perawat OK',
                            \App\Models\User::ROLE_PERAWAT_BIASA => 'Perawat Reguler',
                            \App\Models\User::ROLE_ADMIN => 'Admin',
                            default => 'Pengguna',
                        } }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Terdaftar</p>
                    <p class="mt-2 text-sm text-slate-700">{{ $userItem->created_at?->format('d M Y H:i') ?? '-' }}</p>
                </div>
            </div>
        </div>

        @if ($userItem->role === \App\Models\User::ROLE_DOKTER)
            <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-sm font-semibold text-slate-700">Detail Dokter</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Spesialis</p>
                        <p class="mt-2 text-sm text-slate-700">{{ $userItem->doctor?->specialist?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Title</p>
                        <p class="mt-2 text-sm text-slate-700">{{ $userItem->doctor?->title ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">STR Number</p>
                        <p class="mt-2 text-sm text-slate-700">{{ $userItem->doctor?->str_number ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">SIP Number</p>
                        <p class="mt-2 text-sm text-slate-700">{{ $userItem->doctor?->sip_number ?? '-' }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (in_array($userItem->role, [\App\Models\User::ROLE_PERAWAT_OK, \App\Models\User::ROLE_PERAWAT_BIASA], true))
            <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-sm font-semibold text-slate-700">Detail Perawat</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Jenis Perawat</p>
                        <p class="mt-2 text-sm text-slate-700">
                            {{ $userItem->role === \App\Models\User::ROLE_PERAWAT_OK ? 'Perawat OK' : 'Perawat Reguler' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Unit Asal</p>
                        <p class="mt-2 text-sm text-slate-700">
                            {{ $userItem->role === \App\Models\User::ROLE_PERAWAT_BIASA ? ($userItem->nurse?->origin_unit ?? '-') : '-' }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="flex flex-wrap items-center justify-end gap-3">
            <a
                href="{{ route('admin.users.index') }}"
                class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
            >
                Kembali
            </a>
            <a
                href="{{ route('admin.users.edit', $userItem->id) }}"
                class="inline-flex items-center justify-center rounded-xl border border-emerald-200 px-4 py-2 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50"
            >
                Edit User
            </a>
            <form method="POST" action="{{ route('admin.users.destroy', $userItem->id) }}">
                @csrf
                @method('DELETE')
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl border border-rose-200 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-50"
                    onclick="return confirm('Hapus user ini?')"
                >
                    Hapus User
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
