<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Ringkasan klinis</p>
            <h1 class="text-2xl font-bold text-slate-900">Dashboard Dokter</h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Pasien Hari Ini', 'value' => '18', 'note' => '4 menunggu visit'],
                ['label' => 'Pengajuan Baru', 'value' => '07', 'note' => '2 prioritas tinggi'],
                ['label' => 'Operasi Terjadwal', 'value' => '05', 'note' => 'Hari ini'],
                ['label' => 'Laporan Tertunda', 'value' => '03', 'note' => 'Perlu dilengkapi'],
            ] as $stat)
                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-slate-500">{{ $stat['label'] }}</p>
                    <p class="mt-3 text-3xl font-bold text-slate-900">{{ $stat['value'] }}</p>
                    <p class="mt-2 text-sm text-cyan-700">{{ $stat['note'] }}</p>
                </div>
            @endforeach
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.45fr_0.8fr]">
            <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-lg font-semibold">Pengajuan Operasi Terbaru</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-slate-500">
                            <tr>
                                <th class="px-5 py-3 font-medium">Pasien</th>
                                <th class="px-5 py-3 font-medium">Tindakan</th>
                                <th class="px-5 py-3 font-medium">Tanggal</th>
                                <th class="px-5 py-3 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ([
                                ['patient' => 'Budi Santoso', 'action' => 'Appendektomi', 'date' => '18 Mei', 'status' => 'Menunggu'],
                                ['patient' => 'Siti Rahma', 'action' => 'Laparotomi', 'date' => '18 Mei', 'status' => 'Disetujui'],
                                ['patient' => 'Agus Wijaya', 'action' => 'Debridement', 'date' => '19 Mei', 'status' => 'Review'],
                            ] as $row)
                                <tr>
                                    <td class="px-5 py-4 font-medium text-slate-900">{{ $row['patient'] }}</td>
                                    <td class="px-5 py-4 text-slate-600">{{ $row['action'] }}</td>
                                    <td class="px-5 py-4 text-slate-600">{{ $row['date'] }}</td>
                                    <td class="px-5 py-4">
                                        <span class="rounded-md bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $row['status'] }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Jadwal Operasi</h2>
                <div class="mt-5 space-y-4">
                    @foreach ([
                        ['time' => '08:00', 'patient' => 'Siti Rahma', 'room' => 'OK 1'],
                        ['time' => '10:30', 'patient' => 'Budi Santoso', 'room' => 'OK 2'],
                        ['time' => '13:00', 'patient' => 'Agus Wijaya', 'room' => 'OK 1'],
                    ] as $schedule)
                        <div class="flex items-center justify-between rounded-lg bg-slate-50 p-4">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $schedule['patient'] }}</p>
                                <p class="text-sm text-slate-500">{{ $schedule['room'] }}</p>
                            </div>
                            <span class="text-sm font-semibold text-cyan-700">{{ $schedule['time'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
