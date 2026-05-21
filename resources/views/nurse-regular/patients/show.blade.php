<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Master data</p>
            <h1 class="text-2xl font-bold text-slate-900">Detail Patient</h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-wrap justify-end gap-3">
            <a href="{{ route('nurse-regular.patients.index') }}" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Kembali</a>
            @if ($canManagePatients)
                <a href="{{ route('nurse-regular.patients.edit', $patient) }}" class="rounded-lg bg-cyan-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-cyan-800">Edit Patient</a>
                <a href="{{ route('nurse-regular.surgery-requests.create') }}" class="rounded-lg border border-cyan-200 px-4 py-2.5 text-sm font-semibold text-cyan-700 hover:bg-cyan-50">Ajukan Operasi</a>
            @endif
        </div>

        <section class="grid gap-6 xl:grid-cols-2">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Identitas Patient</h2>
                <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-2">
                    <div><dt class="text-slate-500">No. RM</dt><dd class="font-semibold text-slate-900">{{ $patient->medical_record_number }}</dd></div>
                    <div><dt class="text-slate-500">Nama</dt><dd class="font-semibold text-slate-900">{{ $patient->name }}</dd></div>
                    <div><dt class="text-slate-500">Jenis Kelamin</dt><dd class="font-semibold text-slate-900">{{ $patient->gender }}</dd></div>
                    <div><dt class="text-slate-500">Umur</dt><dd class="font-semibold text-slate-900">{{ $patient->age ?? '-' }}</dd></div>
                    <div><dt class="text-slate-500">Tanggal Lahir</dt><dd class="font-semibold text-slate-900">{{ $patient->birth_date?->format('d M Y') ?? '-' }}</dd></div>
                    <div><dt class="text-slate-500">Ruang Asal</dt><dd class="font-semibold text-slate-900">{{ $patient->origin_room ?? '-' }}</dd></div>
                    <div><dt class="text-slate-500">Telepon</dt><dd class="font-semibold text-slate-900">{{ $patient->phone ?? '-' }}</dd></div>
                    <div>
                        <dt class="text-slate-500">Tanggal Pengajuan</dt>
                        <dd class="font-semibold text-slate-900">{{ $patient->latestSurgeryRequest?->requested_date?->format('d M Y') ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Dibuat Kapan</dt>
                        <dd class="font-semibold text-slate-900">{{ $patient->created_at?->format('d M Y H:i') ?? '-' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-slate-500">Diajukan Oleh</dt>
                        <dd class="font-semibold text-slate-900">
                            {{ $patient->createdBy?->name ?? '-' }}
                            @if ($patient->createdBy?->nurse?->origin_unit)
                                <span class="text-slate-500">· {{ $patient->createdBy->nurse->origin_unit }}</span>
                            @endif
                        </dd>
                    </div>
                    <div class="sm:col-span-2"><dt class="text-slate-500">Alamat</dt><dd class="font-semibold text-slate-900">{{ $patient->address ?? '-' }}</dd></div>
                </dl>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Detail Pengajuan Operasi</h2>
                @if ($patient->latestSurgeryRequest)
                    @php
                        $latestRequest = $patient->latestSurgeryRequest;
                        $latestSchedule = $latestRequest->surgerySchedules
                            ->sortByDesc('surgery_date')
                            ->first();
                        $finalNote = $latestRequest->preoperativeChecklist?->final_note
                            ?? $latestRequest->notes
                            ?? '-';
                    @endphp
                    <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-2">
                        <div><dt class="text-slate-500">Status</dt><dd class="font-semibold text-slate-900">{{ ucfirst($latestRequest->request_status) }}</dd></div>
                        <div>
                            <dt class="text-slate-500">Prioritas</dt>
                            <dd class="font-semibold text-slate-900">
                                <span class="inline-flex items-center rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                                    {{ ucfirst($latestRequest->patient_priority) }}
                                </span>
                            </dd>
                        </div>
                        <div><dt class="text-slate-500">Dokter PJ </dt><dd class="font-semibold text-slate-900">{{ $latestRequest->requestedDoctor?->user?->name ?? '-' }}</dd></div>
                        <div><dt class="text-slate-500">Ruang Operasi</dt><dd class="font-semibold text-slate-900">{{ $latestSchedule?->operatingRoom?->room_name ?? '-' }}</dd></div>
                        <div class="sm:col-span-2"><dt class="text-slate-500">Tindakan</dt><dd class="font-semibold text-slate-900">{{ $latestRequest->procedure_text ?? '-' }}</dd></div>
                        <div class="sm:col-span-2"><dt class="text-slate-500">Diagnosa</dt><dd class="font-semibold text-slate-900">{{ $latestRequest->diagnosis_text ?? '-' }}</dd></div>
                        <div class="sm:col-span-2"><dt class="text-slate-500">Catatan</dt><dd class="font-semibold text-slate-900">{{ $finalNote }}</dd></div>
                    </dl>
                @else
                    <p class="mt-5 text-sm text-slate-500">Belum ada pengajuan operasi untuk patient ini.</p>
                @endif
            </div>
        </section>

        <section class="rounded-lg border border-blue-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                        <path d="M3 12a9 9 0 1 0 9-9v4l3-3-3-3v4A9 9 0 0 0 3 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 7v5l3 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Riwayat Status</h2>
                    <p class="text-sm text-slate-500">Jejak status pengajuan operasi terbaru.</p>
                </div>
            </div>

            <div class="mt-5 overflow-x-auto">
                @php
                    $latestRequest = $patient->latestSurgeryRequest;
                    $statusValue = $latestRequest?->request_status;
                    $statusLabel = $statusValue ? ucfirst($statusValue) : '-';
                    $statusNote = match ($statusValue) {
                        'menunggu' => 'Menunggu persetujuan',
                        'dijadwalkan' => 'Operasi dijadwalkan',
                        'disetujui' => 'Pengajuan disetujui',
                        'ditolak' => 'Pengajuan ditolak',
                        default => 'Pengajuan operasi telah dibuat',
                    };
                    $statusColor = match ($statusValue) {
                        'menunggu' => 'bg-amber-500',
                        'dijadwalkan' => 'bg-sky-500',
                        'disetujui' => 'bg-emerald-500',
                        'ditolak' => 'bg-rose-500',
                        default => 'bg-slate-300',
                    };
                @endphp
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-700">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Tanggal Pengajuan</th>
                            <th class="px-4 py-3 font-semibold">Status</th>
                            <th class="px-4 py-3 font-semibold">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @if ($latestRequest)
                            <tr class="transition hover:bg-slate-50">
                                <td class="px-4 py-3 text-slate-600">{{ $latestRequest->requested_date?->format('d M Y') ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span class="h-2.5 w-2.5 rounded-full {{ $statusColor }}"></span>
                                        <span class="font-semibold text-slate-900">{{ $statusLabel }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $statusNote }}</td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-slate-500">Belum ada status pengajuan.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </section>

        
    </div>
</x-app-layout>
