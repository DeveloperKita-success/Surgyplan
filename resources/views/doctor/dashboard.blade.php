<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Ringkasan klinis</p>
            <h1 class="text-2xl font-bold text-slate-900">Dashboard Dokter</h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2">
            @foreach ($stats as $stat)
                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-slate-500">{{ $stat['label'] }}</p>
                    <p class="mt-3 text-3xl font-bold text-slate-900">{{ $stat['value'] }}</p>
                    <p class="mt-2 text-sm text-cyan-700">{{ $stat['note'] }}</p>
                </div>
            @endforeach
        </section>

        <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-lg font-semibold text-slate-900">Jadwal Operasi</h2>
                <p class="mt-1 text-sm text-slate-500">Menampilkan operasi terjadwal dari dokter dengan spesialis yang sama.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-medium">Nama Pasien</th>
                            <th class="px-5 py-3 font-medium">Nama Dokter</th>
                            <th class="px-5 py-3 font-medium">Tindakan</th>
                            <th class="px-5 py-3 font-medium">Tanggal</th>
                            <th class="px-5 py-3 font-medium">Jam</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($schedules as $schedule)
                            <tr>
                                <td class="px-5 py-4 font-medium text-slate-900">{{ $schedule->patient?->name ?? '-' }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $schedule->doctor?->user?->name ?? '-' }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $schedule->surgeryRequest?->procedure_text ?? '-' }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $schedule->surgery_date?->format('d M Y') ?? '-' }}</td>
                                <td class="px-5 py-4 font-semibold text-cyan-700">{{ \Illuminate\Support\Carbon::parse($schedule->start_time)->format('H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-slate-500">Belum ada jadwal operasi untuk spesialis ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-app-layout>
