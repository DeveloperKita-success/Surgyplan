<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Kontrol akses dan akun</p>
            <h1 class="text-2xl font-bold text-slate-900">Manajemen User</h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Total User', 'value' => $summary['total'] ?? 0],
                ['label' => 'Dokter', 'value' => $summary['doctor'] ?? 0],
                ['label' => 'Perawat OK', 'value' => $summary['nurse_ok'] ?? 0],
                ['label' => 'Perawat Reguler', 'value' => $summary['nurse_regular'] ?? 0],
            ] as $stat)
                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-slate-500">{{ $stat['label'] }}</p>
                    <p class="mt-3 text-3xl font-bold text-slate-900">{{ $stat['value'] }}</p>
                </div>
            @endforeach
        </section>

        <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-4 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold">Daftar User</h2>
                    <p class="text-sm text-slate-500">Pantau user aktif dan status online terbaru.</p>
                </div>
                <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:items-center">
                    <div class="relative w-full sm:w-64">
                        <input
                            type="text"
                            placeholder="Cari nama atau email"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                        />
                    </div>
                    <a
                        href="{{ route('admin.users.create') }}"
                        class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700"
                    >
                        Tambah User
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-medium">Nama</th>
                            <th class="px-5 py-3 font-medium">Email</th>
                            <th class="px-5 py-3 font-medium">Role</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                            <th class="px-5 py-3 font-medium">Terdaftar</th>
                            <th class="px-5 py-3 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($users as $user)
                            <tr>
                                <td class="px-5 py-4 font-medium text-slate-900">{{ $user['name'] }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $user['email'] }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $user['role'] }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold {{ $user['is_online'] ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                        <span class="h-2 w-2 rounded-full {{ $user['is_online'] ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        {{ $user['is_online'] ? 'Online' : 'Offline' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-slate-600">{{ $user['created_at'] }}</td>
                                <td class="px-5 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a
                                            href="{{ route('admin.users.show', $user['id']) }}"
                                            class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50"
                                        >
                                            Detail
                                        </a>
                                        <a
                                            href="{{ route('admin.users.edit', $user['id']) }}"
                                            class="rounded-lg border border-emerald-200 px-3 py-1.5 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-50"
                                        >
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.users.destroy', $user['id']) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-50"
                                                onclick="return confirm('Hapus user ini?')"
                                            >
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-sm text-slate-500">
                                    Belum ada data user.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-app-layout>
