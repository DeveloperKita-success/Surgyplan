<x-app-layout>
    <x-slot name="header">
        <div class="space-y-1">
            <p class="text-sm font-medium text-slate-500">Review Perawat OK</p>
            <h1 class="text-2xl font-bold text-slate-900">Pengajuan Operasi</h1>
            <p class="max-w-2xl text-sm text-slate-500">Pantau pengajuan masuk, bedakan status verifikasi, dan buka detail pasien untuk proses ACC.</p>
        </div>
    </x-slot>

    @php
        $statusMeta = [
            'menunggu' => [
                'label' => 'Menunggu',
                'badge' => 'border-amber-200 bg-amber-50 text-amber-700',
                'dot' => 'bg-amber-500',
                'row' => 'border-l-amber-400',
                'action' => 'Periksa',
                'actionClass' => 'border-cyan-700 bg-cyan-700 text-white hover:bg-cyan-800',
            ],
            'disetujui' => [
                'label' => 'Disetujui',
                'badge' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                'dot' => 'bg-emerald-500',
                'row' => 'border-l-emerald-400',
                'action' => 'Lihat',
                'actionClass' => 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100',
            ],
            'ditolak' => [
                'label' => 'Ditolak',
                'badge' => 'border-rose-200 bg-rose-50 text-rose-700',
                'dot' => 'bg-rose-500',
                'row' => 'border-l-rose-400',
                'action' => 'Lihat',
                'actionClass' => 'border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100',
            ],
            
        ];
        $priorityMeta = [
            'Imminent' => 'border-rose-200 bg-rose-50 text-rose-700',
            'Cito' => 'border-orange-200 bg-orange-50 text-orange-700',
            'Urgent' => 'border-amber-200 bg-amber-50 text-amber-700',
            'Expedited' => 'border-sky-200 bg-sky-50 text-sky-700',
            'Elektif' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
            'imminent' => 'border-rose-200 bg-rose-50 text-rose-700',
            'cito' => 'border-orange-200 bg-orange-50 text-orange-700',
            'urgent' => 'border-amber-200 bg-amber-50 text-amber-700',
            'expedited' => 'border-sky-200 bg-sky-50 text-sky-700',
            'elektif' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
        ];
        $filters = [
            ['label' => 'Semua', 'value' => null, 'count' => $statusCounts['all'], 'class' => 'border-slate-200 bg-white text-slate-700'],
            ['label' => 'Menunggu', 'value' => 'menunggu', 'count' => $statusCounts['menunggu'], 'class' => $statusMeta['menunggu']['badge']],
            ['label' => 'Disetujui', 'value' => 'disetujui', 'count' => $statusCounts['disetujui'], 'class' => $statusMeta['disetujui']['badge']],
            ['label' => 'Ditolak', 'value' => 'ditolak', 'count' => $statusCounts['ditolak'], 'class' => $statusMeta['ditolak']['badge']],
            
        ];
    @endphp

    <div class="space-y-6">
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($filters as $filter)
                @php $isActive = $activeStatus === $filter['value']; @endphp
                <a href="{{ $filter['value'] ? route('nurse-ok.requests.index', ['status' => $filter['value']]) : route('nurse-ok.requests.index') }}"
                    class="rounded-xl border px-4 py-3 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md {{ $isActive ? 'border-cyan-700 bg-cyan-700 text-white' : $filter['class'] }}">
                    <span class="block text-xs font-semibold uppercase tracking-wide {{ $isActive ? 'text-cyan-100' : 'text-current' }}">{{ $filter['label'] }}</span>
                    <span class="mt-1 block text-2xl font-bold">{{ $filter['count'] }}</span>
                </a>
            @endforeach
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-2 border-b border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Daftar Pengajuan</h2>
                    <p class="text-sm text-slate-500">Status berwarna membantu membedakan pengajuan yang masih perlu tindakan.</p>
                </div>
                <span class="rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600">
                    Total {{ $statusCounts['all'] }} pengajuan
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-white text-left text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-bold">Pasien</th>
                            <th class="px-5 py-3 font-bold">Tindakan</th>
                            <th class="px-5 py-3 font-bold">Dokter</th>
                            <th class="px-5 py-3 font-bold">Tanggal Usulan</th>
                            <th class="px-5 py-3 font-bold">Status</th>
                            <th class="px-5 py-3 text-right font-bold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($requests as $request)
                            @php
                                $status = $statusMeta[$request->request_status] ?? [
                                    'label' => ucfirst($request->request_status),
                                    'badge' => 'border-slate-200 bg-slate-50 text-slate-700',
                                    'dot' => 'bg-slate-400',
                                    'row' => 'border-l-slate-300',
                                    'action' => 'Lihat',
                                    'actionClass' => 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50',
                                ];
                                $priorityClass = $priorityMeta[$request->patient_priority] ?? 'border-slate-200 bg-slate-50 text-slate-600';
                            @endphp
                            <tr class="border-l-4 {{ $status['row'] }} transition hover:bg-slate-50/80">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-slate-900">{{ $request->patient->name }}</div>
                                    <div class="mt-1 text-xs font-medium text-slate-500">
                                        RM {{ $request->patient->medical_record_number ?? '-' }} · {{ $request->patient->origin_room ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="max-w-sm font-medium text-slate-800">{{ $request->procedure_text ?? '-' }}</div>
                                    <div class="mt-2 inline-flex rounded-full border px-2.5 py-1 text-xs font-bold {{ $priorityClass }}">
                                        {{ ucfirst($request->patient_priority ?? '-') }}
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-slate-800">{{ $request->requestedDoctor?->user?->name ?? 'Belum dipilih' }}</div>
                                    <div class="mt-1 text-xs text-slate-500">{{ $request->requestedDoctor?->specialist?->name ?? 'Menunggu pilihan dokter' }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-slate-800">{{ $request->requested_date?->format('d M Y') ?? '-' }}</div>
                                    <div class="mt-1 text-xs text-slate-500">
                                        {{ $request->requested_start_time ? substr((string) $request->requested_start_time, 0, 5) : '-' }}
                                        @if ($request->requested_end_time)
                                            - {{ substr((string) $request->requested_end_time, 0, 5) }}
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-bold {{ $status['badge'] }}">
                                        <span class="h-2 w-2 rounded-full {{ $status['dot'] }}"></span>
                                        {{ $status['label'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('nurse-ok.requests.show', $request) }}" class="inline-flex rounded-lg border px-3 py-2 text-xs font-bold transition {{ $status['actionClass'] }}">
                                        {{ $status['action'] }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center">
                                    <div class="mx-auto max-w-sm">
                                        <p class="text-base font-bold text-slate-800">Belum ada pengajuan operasi</p>
                                        <p class="mt-1 text-sm text-slate-500">Pengajuan dari perawat biasa akan muncul di sini untuk ditinjau oleh Perawat OK.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{ $requests->links() }}
    </div>
</x-app-layout>
