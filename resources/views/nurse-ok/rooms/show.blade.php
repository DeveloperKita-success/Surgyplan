<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Perawat OK</p>
            <h1 class="text-2xl font-bold text-slate-900">Detail Kamar Operasi</h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex justify-end gap-3">
            <a href="{{ route('nurse-ok.rooms.index') }}" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Kembali</a>
            <a href="{{ route('nurse-ok.rooms.edit', $room) }}" class="rounded-lg bg-cyan-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-cyan-800">Edit Kamar</a>
        </div>

        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Informasi Kamar</h2>
            <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-2">
                <div><dt class="text-slate-500">Kode</dt><dd class="font-semibold text-slate-900">{{ $room->room_code }}</dd></div>
                <div><dt class="text-slate-500">Nama Kamar</dt><dd class="font-semibold text-slate-900">{{ $room->room_name }}</dd></div>
                <div><dt class="text-slate-500">Kapasitas</dt><dd class="font-semibold text-slate-900">{{ $room->capacity }}</dd></div>
                <div><dt class="text-slate-500">Keterangan</dt><dd class="font-semibold text-slate-900">{{ $room->description ?? '-' }}</dd></div>
                <div><dt class="text-slate-500">Status</dt><dd class="font-semibold text-slate-900">{{ ucfirst($room->status) }}</dd></div>
            </dl>
        </section>

        <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Fasilitas Ruang</h2>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($room->facilities as $facility)
                    <div class="px-5 py-4">
                        <p class="font-semibold text-slate-900">{{ $facility->name }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $facility->description ?? '-' }}</p>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-slate-500">
                        Belum ada fasilitas untuk kamar ini.
                    </div>
                @endforelse
            </div>
        </section>

        <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Jadwal Terkait</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-medium">Patient</th>
                            <th class="px-5 py-3 font-medium">Dokter</th>
                            <th class="px-5 py-3 font-medium">Tanggal</th>
                            <th class="px-5 py-3 font-medium">Jam</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($room->surgerySchedules as $schedule)
                            <tr>
                                <td class="px-5 py-4 text-slate-600">{{ $schedule->patient->name }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $schedule->doctor->user->name }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $schedule->surgery_date?->format('d M Y') }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $schedule->start_time }} - {{ $schedule->end_time }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ ucfirst($schedule->schedule_status) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-slate-500">Belum ada jadwal untuk kamar ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-app-layout>
