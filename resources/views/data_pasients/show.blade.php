<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Master data</p>
            <h1 class="text-2xl font-bold text-slate-900">Detail Pasien</h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-slate-500">Nomor RM</p>
                    <p class="mt-1 text-lg font-bold text-slate-900">{{ $patient->medical_record_number }}</p>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('patients.edit', $patient) }}" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Edit</a>
                    <button type="button" class="rounded-lg bg-cyan-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-cyan-800">Ajukan Operasi</button>
                    <button type="button" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Detail Pengajuan</button>
                    <a href="{{ route('patients.index') }}" class="rounded-lg bg-cyan-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-cyan-800">Kembali</a>
                </div>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-2">
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <p class="text-sm text-slate-500">Nama</p>
                    <p class="mt-1 font-semibold text-slate-900">{{ $patient->name }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <p class="text-sm text-slate-500">Gender</p>
                    <p class="mt-1 font-semibold text-slate-900">{{ $patient->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <p class="text-sm text-slate-500">Tanggal Lahir</p>
                    <p class="mt-1 font-semibold text-slate-900">{{ $patient->birth_date ? $patient->birth_date->format('d M Y') : '-' }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <p class="text-sm text-slate-500">Umur</p>
                    <p class="mt-1 font-semibold text-slate-900">{{ $patient->age ?? '-' }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <p class="text-sm text-slate-500">Ruang Asal</p>
                    <p class="mt-1 font-semibold text-slate-900">{{ $patient->origin_room ?? '-' }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <p class="text-sm text-slate-500">No. HP</p>
                    <p class="mt-1 font-semibold text-slate-900">{{ $patient->phone ?? '-' }}</p>
                </div>
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 md:col-span-2">
                    <p class="text-sm text-slate-500">Alamat</p>
                    <p class="mt-1 whitespace-pre-line font-semibold text-slate-900">{{ $patient->address ?? '-' }}</p>
                </div>

                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 md:col-span-2">
                    <p class="text-sm text-slate-500">Dibuat Oleh</p>
                    <p class="mt-1 font-semibold text-slate-900">{{ $patient->createdBy?->name ?? '-' }}</p>
                </div>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1fr_1fr]">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Ringkasan Pengajuan Pasien</h2>

                @if ($patient->latestSurgeryRequest)
                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Status Terakhir</p>
                            <p class="mt-1 font-semibold text-slate-900">{{ $patient->latestSurgeryRequest->request_status }}</p>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Tanggal Pengajuan</p>
                            <p class="mt-1 font-semibold text-slate-900">{{ optional($patient->latestSurgeryRequest->requested_date)->format('d M Y') }}</p>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Prioritas</p>
                            <p class="mt-1 font-semibold text-slate-900">{{ $patient->latestSurgeryRequest->patient_priority }}</p>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Dokter Tujuan</p>
                            <p class="mt-1 font-semibold text-slate-900">{{ $patient->latestSurgeryRequest->requestedDoctor?->user?->name ?? '-' }}</p>
                        </div>
                    </div>
                @else
                    <p class="mt-4 text-sm text-slate-500">Belum ada pengajuan operasi untuk pasien ini.</p>
                @endif
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Riwayat Pengajuan Singkat</h2>

                <div class="mt-5 space-y-3">
                    @forelse ($patient->surgeryRequests as $request)
                        <div class="block rounded-lg border border-slate-200 bg-slate-50 p-4">
                            <div class="flex items-center justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-slate-900">{{ $request->request_status }}</p>
                                    <p class="mt-1 text-sm text-slate-500">
                                        {{ optional($request->requested_date)->format('d M Y') }} • {{ $request->patient_priority }}
                                    </p>
                                </div>
                                <span class="shrink-0 rounded-md border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-600">Riwayat</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada riwayat pengajuan.</p>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
