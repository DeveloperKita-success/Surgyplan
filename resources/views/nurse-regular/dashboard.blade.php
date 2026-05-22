<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Persiapan pasien dan pengajuan</p>
            <h1 class="text-2xl font-bold text-slate-900">Dashboard Perawat Reguler</h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="grid gap-4 md:grid-cols-3">
            @foreach ($stats as $stat)
                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-slate-500">{{ $stat['label'] }}</p>
                    <p class="mt-3 text-3xl font-bold text-slate-900">{{ $stat['value'] }}</p>
                </div>
            @endforeach
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Pengajuan Operasi Ruang</h2>
                <p class="mt-2 text-sm text-slate-500">
                    Gunakan form khusus untuk mengisi No RM, biodata pasien, diagnosis, tindakan, dan checklist persiapan operasi.
                </p>

                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    @foreach ([
                        ['label' => 'No RM', 'value' => 'Input langsung dari rekam medis'],
                        ['label' => 'Diagnosa', 'value' => 'ICD-10'],
                        ['label' => 'Tindakan', 'value' => 'ICD-9 CM'],
                        ['label' => 'Checklist', 'value' => 'Consent, lab, radiologi, vital, riwayat'],
                    ] as $item)
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm font-medium text-slate-500">{{ $item['label'] }}</p>
                            <p class="mt-2 font-semibold text-slate-900">{{ $item['value'] }}</p>
                        </div>
                    @endforeach
                </div>

                <a href="{{ route('nurse-regular.surgery-requests.create') }}" class="mt-5 inline-flex rounded-lg bg-cyan-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-cyan-800">
                    Buka form pengajuan operasi
                </a>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Detail Flow</h2>
                <div class="mt-5 space-y-4">
                    <div>
                        <p class="text-sm text-slate-500">Ruang asal</p>
                        <p class="font-semibold text-slate-900">IGD, Bangsal, Poli</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Status prioritas</p>
                        <p class="font-semibold text-slate-900">Imminent, Cito, Urgent, Expedited, Elektif</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Upload dokumen</p>
                        <p class="font-semibold text-slate-900">Consent bedah, consent anestesi, dan hasil lab</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Akses</p>
                        <p class="font-semibold text-slate-900">Hanya perawat biasa</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
