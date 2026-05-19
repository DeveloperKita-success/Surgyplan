<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Mode lihat saja</p>
            <h1 class="text-2xl font-bold text-slate-900">Jadwal Operasi</h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="flex flex-wrap gap-2">
            @foreach ([
                ['label' => 'Semua', 'value' => null, 'count' => $statusCounts['all']],
                ['label' => 'Terjadwal', 'value' => 'scheduled', 'count' => $statusCounts['scheduled']],
                ['label' => 'Completed', 'value' => 'completed', 'count' => $statusCounts['completed']],
            ] as $filter)
                <a
                    href="{{ $filter['value'] ? route('doctor.schedules.index', ['status' => $filter['value']]) : route('doctor.schedules.index') }}"
                    class="rounded-lg border px-3 py-2 text-sm font-semibold {{ $activeStatus === $filter['value'] ? 'border-cyan-700 bg-cyan-700 text-white' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}"
                >
                    {{ $filter['label'] }} ({{ $filter['count'] }})
                </a>
            @endforeach
        </div>

        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-medium">Pasien</th>
                            <th class="px-5 py-3 font-medium">Tindakan</th>
                            <th class="px-5 py-3 font-medium">Tanggal</th>
                            <th class="px-5 py-3 font-medium">Jam</th>
                            <th class="px-5 py-3 font-medium">Kamar</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                            <th class="px-5 py-3 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($schedules as $schedule)
                            <tr>
                                <td class="px-5 py-4 font-medium text-slate-900">{{ $schedule->patient->name }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $schedule->surgeryRequest->procedure?->name ?? '-' }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $schedule->surgery_date?->format('d M Y') }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $schedule->start_time }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $schedule->operatingRoom->room_name }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ ucfirst($schedule->schedule_status) }}</td>
                                <td class="px-5 py-4">
                                    <a href="{{ route('doctor.schedules.show', $schedule) }}" class="rounded-md border border-slate-200 px-3 py-1.5 font-medium text-slate-700 hover:bg-slate-50">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-slate-500">Belum ada jadwal operasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{ $schedules->links() }}
    </div>
</x-app-layout>
