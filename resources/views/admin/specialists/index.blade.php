<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Data pendukung admin</p>
            <h1 class="text-2xl font-bold text-slate-900">Spesialis Dokter</h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Total Spesialis</p>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ $summary['total'] ?? 0 }}</p>
            </div>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-4 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold">Daftar Spesialis</h2>
                    <p class="text-sm text-slate-500">Kelola daftar spesialis dokter.</p>
                </div>
                <a
                    href="{{ route('admin.specialists.create') }}"
                    class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700"
                >
                    Tambah Spesialis
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-medium">Nama</th>
                            <th class="px-5 py-3 font-medium">Deskripsi</th>
                            <th class="px-5 py-3 font-medium">Dibuat</th>
                            <th class="px-5 py-3 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($specialists as $specialist)
                            <tr>
                                <td class="px-5 py-4 font-medium text-slate-900">{{ $specialist->name }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $specialist->description ?? '-' }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ optional($specialist->created_at)->format('d M Y') }}</td>
                                <td class="px-5 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a
                                            href="{{ route('admin.specialists.show', $specialist) }}"
                                            class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50"
                                        >
                                            Detail
                                        </a>
                                        <a
                                            href="{{ route('admin.specialists.edit', $specialist) }}"
                                            class="rounded-lg border border-emerald-200 px-3 py-1.5 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-50"
                                        >
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.specialists.destroy', $specialist) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-50"
                                                onclick="return confirm('Hapus spesialis ini?')"
                                            >
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-sm text-slate-500">
                                    Belum ada data spesialis.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-app-layout>
