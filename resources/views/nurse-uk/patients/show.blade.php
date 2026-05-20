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
            <a href="{{ route('nurse-uk.patients.index') }}" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Kembali</a>
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
                    <div><dt class="text-slate-500">Dibuat Oleh</dt><dd class="font-semibold text-slate-900">{{ $patient->createdBy?->name ?? '-' }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-slate-500">Alamat</dt><dd class="font-semibold text-slate-900">{{ $patient->address ?? '-' }}</dd></div>
                </dl>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Pengajuan Terakhir</h2>
                @if ($patient->latestSurgeryRequest)
                    <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-2">
                        <div><dt class="text-slate-500">Status</dt><dd class="font-semibold text-slate-900">{{ ucfirst($patient->latestSurgeryRequest->request_status) }}</dd></div>
                        <div><dt class="text-slate-500">Prioritas</dt><dd class="font-semibold text-slate-900">{{ ucfirst($patient->latestSurgeryRequest->patient_priority) }}</dd></div>
                        <div><dt class="text-slate-500">Tanggal</dt><dd class="font-semibold text-slate-900">{{ $patient->latestSurgeryRequest->requested_date?->format('d M Y') ?? '-' }}</dd></div>
                        <div><dt class="text-slate-500">Dokter</dt><dd class="font-semibold text-slate-900">{{ $patient->latestSurgeryRequest->requestedDoctor?->user?->name ?? '-' }}</dd></div>
                        <div class="sm:col-span-2"><dt class="text-slate-500">Tindakan</dt><dd class="font-semibold text-slate-900">{{ $patient->latestSurgeryRequest->procedure_text ?? '-' }}</dd></div>
                    </dl>
                @else
                    <p class="mt-5 text-sm text-slate-500">Belum ada pengajuan operasi untuk patient ini.</p>
                @endif
            </div>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Riwayat Pengajuan</h2>
            <div class="mt-5 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-500">
                        <tr>
                            <th class="px-4 py-3 font-medium">Tanggal</th>
                            <th class="px-4 py-3 font-medium">Diagnosis</th>
                            <th class="px-4 py-3 font-medium">Tindakan</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($patient->surgeryRequests as $request)
                            <tr>
                                <td class="px-4 py-3 text-slate-600">{{ $request->requested_date?->format('d M Y') ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $request->diagnosis_text ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $request->procedure_text ?? '-' }}</td>
                                <td class="px-4 py-3 font-medium text-slate-900">{{ ucfirst($request->request_status) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-slate-500">Belum ada riwayat pengajuan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-app-layout>
