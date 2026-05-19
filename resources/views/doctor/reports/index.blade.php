<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Mode lihat saja</p>
            <h1 class="text-2xl font-bold text-slate-900">Laporan Operasi</h1>
        </div>
    </x-slot>

    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-slate-500">
                    <tr>
                        <th class="px-5 py-3 font-medium">Pasien</th>
                        <th class="px-5 py-3 font-medium">Tindakan</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 font-medium">Hasil Operasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($reports as $report)
                        <tr>
                            <td class="px-5 py-4 font-medium text-slate-900">{{ $report->surgerySchedule->patient->name }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $report->surgerySchedule->surgeryRequest->procedure?->name ?? '-' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ ucfirst($report->status) }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $report->operation_result ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-slate-500">Belum ada laporan operasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $reports->links() }}
    </div>
</x-app-layout>
