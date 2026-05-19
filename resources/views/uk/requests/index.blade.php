<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Review Perawat UK</p>
            <h1 class="text-2xl font-bold text-slate-900">Pengajuan Operasi</h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="flex flex-wrap gap-2">
            @foreach ([
                ['label' => 'Semua', 'value' => null, 'count' => $statusCounts['all']],
                ['label' => 'Menunggu', 'value' => 'menunggu', 'count' => $statusCounts['menunggu']],
                ['label' => 'Disetujui', 'value' => 'disetujui', 'count' => $statusCounts['disetujui']],
                ['label' => 'Ditolak', 'value' => 'ditolak', 'count' => $statusCounts['ditolak']],
                ['label' => 'Ditunda', 'value' => 'ditunda', 'count' => $statusCounts['ditunda']],
            ] as $filter)
                <a
                    href="{{ $filter['value'] ? route('uk.requests.index', ['status' => $filter['value']]) : route('uk.requests.index') }}"
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
                            <th class="px-5 py-3 font-medium">Dokter</th>
                            <th class="px-5 py-3 font-medium">Tanggal Usulan</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                            <th class="px-5 py-3 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($requests as $request)
                            <tr>
                                <td class="px-5 py-4 font-medium text-slate-900">{{ $request->patient->name }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $request->procedure?->name ?? '-' }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $request->requestedDoctor?->user?->name ?? '-' }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $request->requested_date?->format('d M Y') }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ ucfirst($request->request_status) }}</td>
                                <td class="px-5 py-4">
                                    <a href="{{ route('uk.requests.show', $request) }}" class="rounded-md border border-slate-200 px-3 py-1.5 font-medium text-slate-700 hover:bg-slate-50">
                                        Periksa
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-slate-500">Belum ada pengajuan operasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{ $requests->links() }}
    </div>
</x-app-layout>
