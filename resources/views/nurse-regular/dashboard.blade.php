<x-app-layout>
    @php
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $dashboardStats = [
            ['label' => 'Total Pengajuan Hari Ini', 'value' => $stats[0]['value'] ?? '00', 'icon' => 'records'],
            ['label' => 'Total Pengajuan', 'value' => $stats[1]['value'] ?? '00', 'icon' => 'clipboard'],
            ['label' => 'Total Penjadwalan Operasi', 'value' => $stats[2]['value'] ?? '00', 'icon' => 'calendar'],
        ];
    @endphp

    <div class="space-y-6">
        <section class="grid gap-5 xl:grid-cols-[1fr_320px] xl:items-start">
            <div class="rounded-[2rem] border border-white bg-white/85 p-6 shadow-[0_18px_45px_rgba(15,23,42,0.07)] backdrop-blur">
                <p class="text-sm font-semibold text-slate-500">Selamat datang kembali,</p>
                <div class="mt-3 flex flex-wrap items-center gap-3">
                    <h1 class="text-3xl font-bold tracking-normal text-slate-950">{{ $user?->name ?? 'Perawat Demo' }}</h1>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-teal-800 ring-1 ring-emerald-100">Perawat Reguler</span>
                </div>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-500">
                    Kelola pengajuan operasi pasien, lengkapi dokumen klinis, dan pantau alur persiapan ruang operasi dari satu dashboard.
                </p>
            </div>

            <div class="rounded-[2rem] border border-white/80 bg-white/75 p-5 shadow-[0_18px_45px_rgba(15,23,42,0.07)] backdrop-blur">
                <div class="text-5xl font-bold leading-none text-teal-900/20">"</div>
                <p class="-mt-3 text-sm font-semibold leading-6 text-slate-700">
                    Kesehatan adalah mahkota yang hanya dilihat oleh orang sakit.
                </p>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-3">
            @foreach ($dashboardStats as $stat)
                <div class="rounded-[1.75rem] border border-white bg-white p-5 shadow-[0_16px_40px_rgba(15,23,42,0.06)]">
                    <div class="flex items-start justify-between gap-4">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-teal-50 text-teal-800">
                            @include('layouts.sidebars.icons', ['name' => $stat['icon']])
                        </span>
                        <span class="rounded-full bg-slate-50 px-2.5 py-1 text-[11px] font-bold text-slate-500">Live</span>
                    </div>
                    <p class="mt-5 text-sm font-semibold text-slate-500">{{ $stat['label'] }}</p>
                    <p class="mt-2 text-4xl font-bold tracking-normal text-slate-950">{{ $stat['value'] }}</p>
                    <div class="mt-4 flex h-7 items-end gap-1.5">
                        @foreach ([35, 58, 44, 72, 52, 82, 66] as $height)
                            <span class="w-full rounded-full bg-gradient-to-t from-teal-800 to-emerald-200" style="height: {{ $height }}%"></span>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </section>

        <section class="grid gap-5 xl:grid-cols-[1.35fr_0.65fr]">
            <div class="rounded-[2rem] border border-white bg-white p-6 shadow-[0_18px_45px_rgba(15,23,42,0.07)]">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-slate-950">Pengajuan Operasi Ruang</h2>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                            Gunakan form khusus untuk mengisi No RM, biodata pasien, diagnosis, tindakan, dan checklist persiapan operasi.
                        </p>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    @foreach ([
                        ['label' => 'No RM', 'value' => 'Input langsung dari rekam medis'],
                        ['label' => 'Diagnosa', 'value' => 'ICD-10'],
                        ['label' => 'Tindakan', 'value' => 'ICD-9 CM'],
                        ['label' => 'Checklist', 'value' => 'Consent, lab, radiologi, vital, riwayat'],
                    ] as $item)
                        <div class="rounded-3xl border border-slate-100 bg-slate-50/80 p-4">
                            <p class="text-sm font-bold text-slate-900">{{ $item['label'] }}</p>
                            <p class="mt-2 text-sm font-medium leading-6 text-slate-500">{{ $item['value'] }}</p>
                        </div>
                    @endforeach
                </div>

                <a href="{{ route('nurse-regular.surgery-requests.create') }}" class="mt-6 inline-flex items-center gap-2 rounded-2xl bg-teal-900 px-5 py-3 text-sm font-bold text-white shadow-xl shadow-teal-900/15 transition hover:-translate-y-0.5 hover:bg-teal-800">
                    Buka form pengajuan operasi
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                        <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>

            <div class="rounded-[2rem] border border-white bg-white p-6 shadow-[0_18px_45px_rgba(15,23,42,0.07)]">
                <h2 class="text-xl font-bold text-slate-950">Detail Flow</h2>
                <div class="mt-6 space-y-5">
                    @foreach ([
                        ['label' => 'Ruang asal', 'value' => 'IGD, Bangsal, Poli'],
                        ['label' => 'Status prioritas', 'value' => 'Imminent, Cito, Urgent, Expedited, Elektif'],
                        ['label' => 'Upload dokumen', 'value' => 'Consent bedah, consent anestesi, dan hasil lab'],
                        ['label' => 'Akses', 'value' => 'Hanya perawat biasa'],
                    ] as $item)
                        <div class="relative pl-8">
                            <span class="absolute left-0 top-1 flex h-4 w-4 items-center justify-center rounded-full bg-emerald-100 ring-4 ring-emerald-50">
                                <span class="h-2 w-2 rounded-full bg-teal-700"></span>
                            </span>
                            @if (! $loop->last)
                                <span class="absolute bottom-[-1.35rem] left-[7px] top-6 w-px bg-emerald-100"></span>
                            @endif
                            <p class="text-sm font-bold text-slate-950">{{ $item['label'] }}</p>
                            <p class="mt-1 text-sm leading-6 text-slate-500">{{ $item['value'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
