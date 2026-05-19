<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Kelola pengajuan</p>
            <h1 class="text-2xl font-bold text-slate-900">Pengajuan Operasi</h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex flex-wrap gap-2">
                @foreach ([
                    ['label' => 'Semua', 'value' => null, 'count' => $statusCounts['all']],
                    ['label' => 'Menunggu', 'value' => 'menunggu', 'count' => $statusCounts['menunggu']],
                    ['label' => 'Disetujui', 'value' => 'disetujui', 'count' => $statusCounts['disetujui']],
                    ['label' => 'Ditolak', 'value' => 'ditolak', 'count' => $statusCounts['ditolak']],
                ] as $filter)
                    <a
                        href="{{ $filter['value'] ? route('nurse-regular.surgery-requests.index', ['status' => $filter['value']]) : route('nurse-regular.surgery-requests.index') }}"
                        class="rounded-lg border px-3 py-2 text-sm font-semibold {{ $activeStatus === $filter['value'] ? 'border-cyan-700 bg-cyan-700 text-white' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}"
                    >
                        {{ $filter['label'] }} ({{ $filter['count'] }})
                    </a>
                @endforeach
            </div>
            <a href="{{ route('nurse-regular.surgery-requests.create') }}" class="rounded-lg bg-cyan-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-cyan-800">
                Buat Pengajuan
            </a>
        </div>

        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-medium">Pasien</th>
                            <th class="px-5 py-3 font-medium">Tindakan</th>
                            <th class="px-5 py-3 font-medium">Dokter</th>
                            <th class="px-5 py-3 font-medium">Tanggal</th>
                            <th class="px-5 py-3 font-medium">Status Pasien</th>
                            <th class="px-5 py-3 font-medium">Status Pengajuan</th>
                            <th class="px-5 py-3 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($requests as $request)
                            @php
                                $priorityClass = match ($request->patient_priority) {
                                    'imminent' => 'bg-rose-100 text-rose-700',
                                    'cito' => 'bg-orange-100 text-orange-700',
                                    'urgent' => 'bg-amber-100 text-amber-700',
                                    'expedited' => 'bg-sky-100 text-sky-700',
                                    default => 'bg-emerald-100 text-emerald-700',
                                };
                            @endphp
                            <tr>
                                <td class="px-5 py-4 font-medium text-slate-900">{{ $request->patient->name }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $request->procedure?->name ?? '-' }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $request->requestedDoctor?->user?->name ?? '-' }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $request->requested_date?->format('d M Y') }}</td>
                                <td class="px-5 py-4">
                                    <span class="rounded-md px-2.5 py-1 text-xs font-semibold {{ $priorityClass }}">
                                        {{ ucfirst($request->patient_priority) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="rounded-md bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                        {{ ucfirst($request->request_status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('nurse-regular.surgery-requests.show', $request) }}" class="rounded-md border border-slate-200 px-3 py-1.5 font-medium text-slate-700 hover:bg-slate-50">
                                            Detail
                                        </a>
                                        @if ($request->request_status === 'menunggu')
                                            <a href="{{ route('nurse-regular.surgery-requests.edit', $request) }}" class="rounded-md border border-cyan-200 px-3 py-1.5 font-medium text-cyan-700 hover:bg-cyan-50">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('nurse-regular.surgery-requests.destroy', $request) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-md border border-rose-200 px-3 py-1.5 font-medium text-rose-700 hover:bg-rose-50">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-slate-500">Belum ada pengajuan operasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{ $requests->links() }}
    </div>
</x-app-layout>
