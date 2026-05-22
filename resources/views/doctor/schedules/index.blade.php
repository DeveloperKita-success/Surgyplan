<x-app-layout>
    <x-slot name="header">
        <div class="space-y-2">
            <p class="text-sm font-semibold text-slate-500">Master Data</p>
            <h1 class="text-3xl font-extrabold tracking-normal text-slate-950">JADWAL OPERASI</h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="rounded-3xl border border-slate-200/80 bg-white/90 p-5 shadow-sm ring-1 ring-white/60 backdrop-blur">
            <form method="GET" action="{{ route('doctor.schedules.index') }}" class="grid gap-4 xl:grid-cols-[minmax(260px,1fr)_220px_auto_auto] xl:items-end">
                <div>
                    <label for="q" class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Cari</label>
                    <div class="mt-2 flex h-12 items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 text-slate-500 focus-within:border-emerald-400 focus-within:bg-white focus-within:ring-4 focus-within:ring-emerald-100">
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <input
                            id="q"
                            name="q"
                            value="{{ $search }}"
                            type="search"
                            placeholder="Cari nama dokter"
                            class="h-full min-w-0 flex-1 border-0 bg-transparent p-0 text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:ring-0"
                        >
                    </div>
                </div>

                <div>
                    <label for="date" class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Tanggal</label>
                    <input
                        id="date"
                        name="date"
                        value="{{ $selectedDate }}"
                        type="date"
                        class="mt-2 h-12 w-full rounded-2xl border-slate-200 bg-slate-50 px-4 text-sm font-semibold text-slate-700 focus:border-emerald-400 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                    >
                </div>

                <button type="submit" class="inline-flex h-12 items-center justify-center rounded-2xl bg-slate-950 px-6 text-sm font-bold text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-300">
                    Terapkan
                </button>

                <a href="{{ route('doctor.schedules.index') }}" class="inline-flex h-12 items-center justify-center rounded-2xl border border-slate-200 bg-white px-6 text-sm font-bold text-slate-800 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-200">
                    Reset
                </a>
            </form>
        </section>

        <section class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white/90 shadow-sm ring-1 ring-white/60 backdrop-blur">
            <div class="overflow-x-auto">
                <table class="min-w-[1180px] divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-100/80 text-left text-xs font-bold uppercase tracking-[0.12em] text-slate-500">
                        <tr>
                            <th class="px-5 py-4">No</th>
                            <th class="px-5 py-4">Nama Dokter</th>
                            <th class="px-5 py-4">No RM</th>
                            <th class="px-5 py-4">Nama Pasien</th>
                            <th class="px-5 py-4">Tanggal</th>
                            <th class="px-5 py-4">Jam</th>
                            <th class="px-5 py-4">Jenis</th>
                            <th class="px-5 py-4">Kamar</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4">Asal</th>
                            <th class="px-5 py-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($schedules as $schedule)
                            <tr class="text-slate-700 transition hover:bg-emerald-50/50">
                                <td class="px-5 py-4 font-semibold text-slate-500">{{ $schedules->firstItem() + $loop->index }}</td>
                                <td class="px-5 py-4 font-semibold text-slate-950">{{ $schedule->doctor?->user?->name ?? '-' }}</td>
                                <td class="px-5 py-4">{{ $schedule->patient?->medical_record_number ?? '-' }}</td>
                                <td class="px-5 py-4 font-semibold text-slate-900">{{ $schedule->patient?->name ?? '-' }}</td>
                                <td class="px-5 py-4">{{ $schedule->surgery_date?->format('d M Y') ?? '-' }}</td>
                                <td class="px-5 py-4 font-bold text-emerald-700">{{ \Illuminate\Support\Carbon::parse($schedule->start_time)->format('H:i') }}</td>
                                <td class="px-5 py-4">{{ $schedule->surgeryRequest?->procedure_text ?? '-' }}</td>
                                <td class="px-5 py-4">{{ $schedule->operatingRoom?->room_name ?? '-' }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">
                                        {{ ucfirst($schedule->schedule_status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">{{ $schedule->patient?->origin_room ?? '-' }}</td>
                                <td class="px-5 py-4">
                                    <a href="{{ route('doctor.schedules.show', $schedule) }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 shadow-sm hover:bg-slate-50">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-5 py-16 text-center text-sm font-semibold text-slate-500">
                                    Belum ada data pasien
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{ $schedules->links() }}
    </div>
</x-app-layout>
