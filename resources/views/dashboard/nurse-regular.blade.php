<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Persiapan pasien dan pengajuan</p>
            <h1 class="text-2xl font-bold text-slate-900">Dashboard Perawat Reguler</h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Pasien Ditangani', 'value' => '24'],
                ['label' => 'Pengajuan Draft', 'value' => '05'],
                ['label' => 'Checklist Belum Lengkap', 'value' => '08'],
                ['label' => 'Jadwal Hari Ini', 'value' => '06'],
            ] as $stat)
                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-slate-500">{{ $stat['label'] }}</p>
                    <p class="mt-3 text-3xl font-bold text-slate-900">{{ $stat['value'] }}</p>
                </div>
            @endforeach
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Form Pengajuan Cepat</h2>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Nama Pasien</span>
                        <input type="text" placeholder="Masukkan nama pasien" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                    </label>
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Dokter Tujuan</span>
                        <select class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                            <option>Pilih dokter</option>
                            <option>dr. Andi</option>
                            <option>dr. Maya</option>
                        </select>
                    </label>
                    <label class="space-y-2 md:col-span-2">
                        <span class="text-sm font-medium text-slate-600">Catatan Klinis</span>
                        <textarea rows="4" placeholder="Ringkasan kondisi pasien" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600"></textarea>
                    </label>
                </div>
                <button type="button" class="mt-5 rounded-lg bg-cyan-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-cyan-800">
                    Simpan Draft
                </button>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Detail Pasien</h2>
                <div class="mt-5 space-y-4">
                    <div>
                        <p class="text-sm text-slate-500">Nama</p>
                        <p class="font-semibold text-slate-900">Budi Santoso</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Unit Asal</p>
                        <p class="font-semibold text-slate-900">{{ optional(auth()->user()->nurse)->origin_unit ?? 'IGD' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Status Checklist</p>
                        <p class="font-semibold text-amber-700">6 dari 8 item selesai</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Jadwal</p>
                        <p class="font-semibold text-slate-900">18 Mei 2026, 10:30</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
