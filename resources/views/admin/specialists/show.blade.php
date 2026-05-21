<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Data pendukung admin</p>
            <h1 class="text-2xl font-bold text-slate-900">Detail Spesialis</h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-sm text-slate-500">Nama Spesialis</p>
                    <p class="mt-2 text-lg font-semibold text-slate-900">{{ $specialist->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Deskripsi</p>
                    <p class="mt-2 text-slate-700">{{ $specialist->description ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Total Dokter</p>
                    <p class="mt-2 text-lg font-semibold text-slate-900">{{ $specialist->doctors_count ?? 0 }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Total Kamar Operasi</p>
                    <p class="mt-2 text-lg font-semibold text-slate-900">{{ $specialist->operating_rooms_count ?? 0 }}</p>
                </div>
            </div>
        </section>

        <div class="flex flex-wrap items-center justify-end gap-3">
            <a
                href="{{ route('admin.specialists.index') }}"
                class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
            >
                Kembali
            </a>
            <a
                href="{{ route('admin.specialists.edit', $specialist) }}"
                class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700"
            >
                Edit
            </a>
        </div>
    </div>
</x-app-layout>
