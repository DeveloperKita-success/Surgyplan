<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Perawat UK</p>
            <h1 class="text-2xl font-bold text-slate-900">Jadwal Operasi</h1>
        </div>
    </x-slot>

    @php
        $monthNames = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
        $dayNames = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
    @endphp

    <div class="space-y-6">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <form method="GET" action="{{ route('nurse-uk.schedules.index') }}" class="grid gap-3 md:grid-cols-[220px_160px_auto] md:items-end">
                <div>
                    <label for="month" class="text-sm font-semibold text-slate-700">Bulan</label>
                    <select id="month" name="month" class="mt-2 w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                        @foreach ($monthNames as $monthNumber => $monthName)
                            <option value="{{ $monthNumber }}" @selected($month === $monthNumber)>{{ $monthName }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="year" class="text-sm font-semibold text-slate-700">Tahun</label>
                    <select id="year" name="year" class="mt-2 w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                        @foreach ($years as $yearOption)
                            <option value="{{ $yearOption }}" @selected($year === $yearOption)>{{ $yearOption }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="rounded-lg bg-cyan-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-cyan-800">
                    Tampilkan
                </button>
            </form>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.25fr_0.75fr]">
            <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-5 py-4">
                    <div>
                        <p class="text-sm text-slate-500">Kalender operasi disetujui</p>
                        <h2 class="text-lg font-semibold text-slate-900">{{ $monthNames[$month] }} {{ $year }}</h2>
                    </div>
                    <div class="flex gap-2 text-xs font-semibold text-slate-600">
                        <span class="rounded-md bg-cyan-50 px-2.5 py-1 text-cyan-700">Ada jadwal</span>
                        <span class="rounded-md bg-slate-100 px-2.5 py-1">Kosong</span>
                    </div>
                </div>

                <div class="grid grid-cols-7 border-b border-slate-200 bg-slate-50 text-center text-xs font-semibold uppercase text-slate-500">
                    @foreach ($dayNames as $dayName)
                        <div class="px-2 py-3">{{ $dayName }}</div>
                    @endforeach
                </div>

                <div class="grid grid-cols-7 bg-slate-100">
                    @foreach ($calendarWeeks as $week)
                        @foreach ($week as $day)
                            @php
                                $isSelected = $day['date']->isSameDay($selectedDate);
                                $dayUrl = route('nurse-uk.schedules.index', [
                                    'month' => $month,
                                    'year' => $year,
                                    'date' => $day['date']->toDateString(),
                                ]);
                            @endphp

                            <a
                                href="{{ $dayUrl }}"
                                class="min-h-[112px] border-b border-r border-slate-200 bg-white p-3 text-left transition hover:bg-cyan-50 {{ ! $day['inMonth'] ? 'opacity-45' : '' }} {{ $isSelected ? 'ring-2 ring-inset ring-cyan-700' : '' }}"
                            >
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-sm font-bold {{ $isSelected ? 'text-cyan-800' : 'text-slate-800' }}">
                                        {{ $day['date']->day }}
                                    </span>
                                    @if ($day['count'] > 0)
                                        <span class="rounded-full bg-cyan-700 px-2 py-0.5 text-xs font-semibold text-white">
                                            {{ $day['count'] }}
                                        </span>
                                    @endif
                                </div>

                                <div class="mt-4 space-y-1 text-xs">
                                    @if ($day['count'] > 0)
                                        <p class="font-semibold text-slate-700">{{ $day['count'] }} jadwal</p>
                                        <p class="text-slate-500">{{ $day['bookedRoomCount'] }} kamar terpakai</p>
                                    @else
                                        <p class="text-slate-400">Tidak ada jadwal</p>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    @endforeach
                </div>
            </div>

            <aside class="space-y-6">
                <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-slate-500">Tanggal dipilih</p>
                    <h2 class="mt-1 text-lg font-semibold text-slate-900">{{ $selectedDate->translatedFormat('d F Y') }}</h2>

                    <div class="mt-5 grid grid-cols-3 gap-3 text-center">
                        <div class="rounded-lg bg-slate-50 p-3">
                            <p class="text-xs text-slate-500">Total Kamar</p>
                            <p class="mt-1 text-xl font-bold text-slate-900">{{ $roomStats['total'] }}</p>
                        </div>
                        <div class="rounded-lg bg-rose-50 p-3">
                            <p class="text-xs text-rose-600">Terpakai</p>
                            <p class="mt-1 text-xl font-bold text-rose-700">{{ $roomStats['booked'] }}</p>
                        </div>
                        <div class="rounded-lg bg-emerald-50 p-3">
                            <p class="text-xs text-emerald-600">Tersedia</p>
                            <p class="mt-1 text-xl font-bold text-emerald-700">{{ $roomStats['available'] }}</p>
                        </div>
                    </div>
                </section>

                <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 px-5 py-4">
                        <h3 class="font-semibold text-slate-900">Jadwal Tanggal Ini</h3>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse ($selectedSchedules as $schedule)
                            <a href="{{ route('nurse-uk.requests.show', $schedule->surgeryRequest) }}" class="block px-5 py-4 hover:bg-slate-50">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $schedule->patient->name }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ $schedule->surgeryRequest->procedure_text ?? '-' }}</p>
                                    </div>
                                    <span class="rounded-md bg-cyan-50 px-2.5 py-1 text-xs font-semibold text-cyan-700">
                                        {{ substr((string) $schedule->start_time, 0, 5) }} - {{ substr((string) $schedule->end_time, 0, 5) }}
                                    </span>
                                </div>
                                <dl class="mt-3 grid gap-2 text-sm text-slate-600">
                                    <div class="flex justify-between gap-3"><dt>Dokter</dt><dd class="font-medium text-slate-800">{{ $schedule->doctor->user->name }}</dd></div>
                                    <div class="flex justify-between gap-3"><dt>Kamar</dt><dd class="font-medium text-slate-800">{{ $schedule->operatingRoom->room_name }}</dd></div>
                                </dl>
                            </a>
                        @empty
                            <div class="px-5 py-8 text-center text-sm text-slate-500">
                                Belum ada operasi yang disetujui pada tanggal ini.
                            </div>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 px-5 py-4">
                        <h3 class="font-semibold text-slate-900">Kamar Masih Tersedia</h3>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse ($availableRooms as $room)
                            <div class="flex items-center justify-between gap-3 px-5 py-3 text-sm">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $room->room_name }}</p>
                                    <p class="text-slate-500">{{ $room->room_code }} · {{ $room->specialist?->name ?? 'Umum' }}</p>
                                </div>
                                <span class="rounded-md bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Siap</span>
                            </div>
                        @empty
                            <div class="px-5 py-8 text-center text-sm text-slate-500">
                                Tidak ada kamar siap yang kosong pada tanggal ini.
                            </div>
                        @endforelse
                    </div>
                </section>
            </aside>
        </section>
    </div>
</x-app-layout>
