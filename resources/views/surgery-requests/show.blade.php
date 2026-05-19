<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Detail pengajuan</p>
            <h1 class="text-2xl font-bold text-slate-900">{{ $surgeryRequest->patient->name }}</h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-wrap justify-end gap-3">
            <a href="{{ route('nurse-regular.surgery-requests.index') }}" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Kembali
            </a>
            @if ($surgeryRequest->request_status === 'menunggu')
                <a href="{{ route('nurse-regular.surgery-requests.edit', $surgeryRequest) }}" class="rounded-lg bg-cyan-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-cyan-800">
                    Edit Pengajuan
                </a>
            @endif
        </div>

        <section class="grid gap-6 xl:grid-cols-2">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Data Pasien</h2>
                <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-2">
                    <div><dt class="text-slate-500">No RM</dt><dd class="font-semibold text-slate-900">{{ $surgeryRequest->patient->medical_record_number }}</dd></div>
                    <div><dt class="text-slate-500">Jenis Kelamin</dt><dd class="font-semibold text-slate-900">{{ $surgeryRequest->patient->gender }}</dd></div>
                    <div><dt class="text-slate-500">Tanggal Lahir</dt><dd class="font-semibold text-slate-900">{{ $surgeryRequest->patient->birth_date?->format('d M Y') ?? '-' }}</dd></div>
                    <div><dt class="text-slate-500">Umur</dt><dd class="font-semibold text-slate-900">{{ $surgeryRequest->patient->age ?? '-' }}</dd></div>
                    <div><dt class="text-slate-500">Ruang Asal</dt><dd class="font-semibold text-slate-900">{{ $surgeryRequest->patient->origin_room }}</dd></div>
                    <div><dt class="text-slate-500">Status Pengajuan</dt><dd class="font-semibold text-slate-900">{{ ucfirst($surgeryRequest->request_status) }}</dd></div>
                </dl>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Rencana Operasi</h2>
                <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-2">
                    <div><dt class="text-slate-500">Diagnosis</dt><dd class="font-semibold text-slate-900">{{ $surgeryRequest->diagnosis?->name ?? '-' }}</dd></div>
                    <div><dt class="text-slate-500">Tindakan</dt><dd class="font-semibold text-slate-900">{{ $surgeryRequest->procedure?->name ?? '-' }}</dd></div>
                    <div><dt class="text-slate-500">Dokter</dt><dd class="font-semibold text-slate-900">{{ $surgeryRequest->requestedDoctor?->user?->name ?? '-' }}</dd></div>
                    <div><dt class="text-slate-500">Status Pasien</dt><dd class="font-semibold text-slate-900">{{ ucfirst($surgeryRequest->patient_priority) }}</dd></div>
                    <div><dt class="text-slate-500">Tanggal</dt><dd class="font-semibold text-slate-900">{{ $surgeryRequest->requested_date?->format('d M Y') }}</dd></div>
                    <div><dt class="text-slate-500">Jam</dt><dd class="font-semibold text-slate-900">{{ $surgeryRequest->requested_start_time }}</dd></div>
                </dl>
            </div>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold">Riwayat Perubahan</h2>
            <div class="mt-5 space-y-3">
                @forelse ($surgeryRequest->surgeryHistories as $history)
                    <div class="rounded-lg bg-slate-50 p-4 text-sm">
                        <p class="font-semibold text-slate-900">{{ $history->note }}</p>
                        <p class="mt-1 text-slate-500">
                            {{ $history->changedBy->name }} · {{ $history->created_at?->format('d M Y H:i') }}
                        </p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada riwayat perubahan.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
