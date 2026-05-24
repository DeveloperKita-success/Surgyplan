<x-app-layout>
    @php
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $dashboardStats = [
            ['label' => 'Total Pengajuan Hari Ini', 'value' => '00', 'icon' => 'records'],
            ['label' => $stats[0]['label'] ?? 'Total Pengajuan', 'value' => $stats[0]['value'] ?? '00', 'icon' => 'clipboard'],
            ['label' => $stats[1]['label'] ?? 'Total Penjadwalan Operasi', 'value' => $stats[1]['value'] ?? '00', 'icon' => 'calendar'],
        ];
    @endphp

    <div class="space-y-6">
        <section class="grid gap-5 xl:grid-cols-[1fr_320px] xl:items-start">
            <div class="rounded-[2rem] border border-white bg-white/85 p-6 shadow-[0_18px_45px_rgba(15,23,42,0.07)] backdrop-blur">
                <p class="text-sm font-semibold text-slate-500">Selamat datang kembali,</p>
                <div class="mt-3 flex flex-wrap items-center gap-3">
                    <h1 class="text-3xl font-bold tracking-normal text-slate-950">{{ $user?->name ?? 'Dokter Demo' }}</h1>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-teal-800 ring-1 ring-emerald-100">Dokter</span>
                </div>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-500">
                    Pantau jadwal operasi, kesiapan tindakan, dan ringkasan aktivitas spesialis dalam tampilan yang ringkas.
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
                    <div class="flex items-start justify-end gap-4">
                        <span class="rounded-full bg-slate-50 px-2.5 py-1 text-[11px] font-bold text-slate-500">Live</span>
                    </div>
                    <p class="mt-5 text-sm font-semibold text-slate-500">{{ $stat['label'] }}</p>
                    <p class="mt-2 text-4xl font-bold tracking-normal text-slate-950">{{ $stat['value'] }}</p>
                </div>
            @endforeach
        </section>

        <section class="grid gap-5 xl:grid-cols-[1.35fr_0.65fr]">
            <div class="overflow-hidden rounded-[2rem] border border-white bg-white shadow-[0_18px_45px_rgba(15,23,42,0.07)]">
                <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-100 px-6 py-5">
                    <div>
                        <h2 class="text-xl font-bold text-slate-950">Jadwal Operasi</h2>
                        <p class="mt-2 text-sm text-slate-500">Menampilkan operasi terjadwal dari dokter dengan spesialis yang sama.</p>
                    </div>
                    <a href="{{ route('doctor.schedules.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-teal-900 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-teal-900/15 transition hover:bg-teal-800">
                        Lihat semua
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                            <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead class="bg-slate-50 text-left text-slate-500">
                            <tr>
                                <th class="px-6 py-4 font-bold">Nama Pasien</th>
                                <th class="px-6 py-4 font-bold">Tindakan</th>
                                <th class="px-6 py-4 font-bold">Tanggal</th>
                                <th class="px-6 py-4 font-bold">Jam</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($schedules as $schedule)
                                <tr class="bg-white">
                                    <td class="px-6 py-4 font-bold text-slate-900">{{ $schedule->patient?->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $schedule->surgeryRequest?->procedure_text ?? '-' }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $schedule->surgery_date?->format('d M Y') ?? '-' }}</td>
                                    <td class="px-6 py-4 font-bold text-teal-800">{{ \Illuminate\Support\Carbon::parse($schedule->start_time)->format('H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-slate-500">Belum ada jadwal operasi untuk spesialis ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-[2rem] border border-white bg-white p-6 shadow-[0_18px_45px_rgba(15,23,42,0.07)]">
                <h2 class="text-xl font-bold text-slate-950">Detail Flow</h2>
                <div class="mt-6 space-y-5">
                    @foreach ([
                        ['label' => 'Review jadwal', 'value' => 'Pantau pasien dan tindakan operasi'],
                        ['label' => 'Prioritas klinis', 'value' => 'Cito, urgent, elektif, dan kategori lain'],
                        ['label' => 'Dokumen pasien', 'value' => 'Diagnosis, tindakan, dan lampiran penunjang'],
                        ['label' => 'Akses', 'value' => 'Khusus dokter sesuai spesialis'],
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
