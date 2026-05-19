<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Verifikasi Perawat UK</p>
            <h1 class="text-2xl font-bold text-slate-900">{{ $surgeryRequest->patient->name }}</h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <section class="grid gap-6 xl:grid-cols-2">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Data Pasien</h2>
                <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-2">
                    <div><dt class="text-slate-500">No RM</dt><dd class="font-semibold">{{ $surgeryRequest->patient->medical_record_number }}</dd></div>
                    <div><dt class="text-slate-500">Ruang Asal</dt><dd class="font-semibold">{{ $surgeryRequest->patient->origin_room }}</dd></div>
                    <div><dt class="text-slate-500">Diagnosis</dt><dd class="font-semibold">{{ $surgeryRequest->diagnosis?->name ?? '-' }}</dd></div>
                    <div><dt class="text-slate-500">Tindakan</dt><dd class="font-semibold">{{ $surgeryRequest->procedure?->name ?? '-' }}</dd></div>
                </dl>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Usulan Operasi</h2>
                <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-2">
                    <div><dt class="text-slate-500">Dokter</dt><dd class="font-semibold">{{ $surgeryRequest->requestedDoctor?->user?->name ?? '-' }}</dd></div>
                    <div><dt class="text-slate-500">Status</dt><dd class="font-semibold">{{ ucfirst($surgeryRequest->request_status) }}</dd></div>
                    <div><dt class="text-slate-500">Tanggal</dt><dd class="font-semibold">{{ $surgeryRequest->requested_date?->format('d M Y') }}</dd></div>
                    <div><dt class="text-slate-500">Jam</dt><dd class="font-semibold">{{ $surgeryRequest->requested_start_time }}</dd></div>
                </dl>
            </div>
        </section>

        @php($checklist = $surgeryRequest->preoperativeChecklist)
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold">Checklist Praoperasi</h2>
            @if ($checklist)
                <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-2 xl:grid-cols-3">
                    <div><dt class="text-slate-500">Consent Bedah</dt><dd class="font-semibold">{{ $checklist->surgical_consent ? 'Ada' : 'Belum ada' }}</dd></div>
                    <div><dt class="text-slate-500">Consent Anestesi</dt><dd class="font-semibold">{{ $checklist->anesthesia_consent ? 'Ada' : 'Belum ada' }}</dd></div>
                    <div><dt class="text-slate-500">Hasil Lab</dt><dd class="font-semibold">{{ $checklist->lab_result_complete ? 'Lengkap' : 'Belum lengkap' }}</dd></div>
                    <div><dt class="text-slate-500">Radiologi</dt><dd class="font-semibold">{{ $checklist->radiology_available ? 'Tersedia' : 'Tidak tersedia' }}</dd></div>
                    <div><dt class="text-slate-500">Konsultasi Anestesi</dt><dd class="font-semibold">{{ $checklist->anesthesia_consultation_done ? 'Sudah' : 'Belum' }}</dd></div>
                    <div><dt class="text-slate-500">Tanda Vital</dt><dd class="font-semibold">{{ $checklist->vital_sign_stable ? 'Stabil' : 'Beresiko' }}</dd></div>
                </dl>
            @else
                <p class="mt-4 text-sm text-slate-500">Checklist praoperasi belum tersedia.</p>
            @endif
        </section>

        @if (in_array($surgeryRequest->request_status, ['menunggu', 'ditunda'], true))
            <form method="POST" action="{{ route('uk.requests.decide', $surgeryRequest) }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                @csrf
                <h2 class="text-lg font-semibold">Checklist Verifikasi Operasi</h2>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    @foreach ([
                        ['patient_wristband_installed', 'Gelang pasien terpasang'],
                        ['doctor_present', 'Dokter hadir'],
                        ['operating_room_ready', 'Kamar operasi siap'],
                        ['anesthesia_approved', 'Anestesi disetujui'],
                    ] as [$name, $label])
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-slate-600">{{ $label }}</span>
                            <select name="{{ $name }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                                <option value="1">Ya</option>
                                <option value="0">Tidak</option>
                            </select>
                        </label>
                    @endforeach
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Saturasi Oksigen</span>
                        <input name="oxygen_saturation" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                    </label>
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Nama Dokter Anestesi</span>
                        <input name="anesthesiologist_name" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                    </label>
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Jenis Anestesi</span>
                        <input name="anesthesia_type" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                    </label>
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">ASA Status</span>
                        <input name="asa_status" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                    </label>
                    <label class="space-y-2 md:col-span-2">
                        <span class="text-sm font-medium text-slate-600">Catatan Anestesi</span>
                        <textarea name="anesthesia_note" rows="2" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600"></textarea>
                    </label>
                    <label class="space-y-2 md:col-span-2">
                        <span class="text-sm font-medium text-slate-600">Catatan Verifikasi</span>
                        <textarea name="verification_note" rows="2" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600"></textarea>
                    </label>
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Kamar Operasi</span>
                        <select name="operating_room_id" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                            <option value="">Pilih kamar</option>
                            @foreach ($rooms as $room)
                                <option value="{{ $room->id }}">{{ $room->room_name }}{{ $room->specialist ? ' ('.$room->specialist->name.')' : '' }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Jam Selesai</span>
                        <input type="time" name="end_time" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                    </label>
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Keputusan</span>
                        <select name="decision" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                            <option value="disetujui">Setujui</option>
                            <option value="ditolak">Tolak</option>
                            <option value="ditunda">Tunda</option>
                        </select>
                    </label>
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Alasan jika ditolak/ditunda</span>
                        <input name="reason" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                    </label>
                </div>
                <div class="mt-5 flex justify-end">
                    <button type="submit" class="rounded-lg bg-cyan-700 px-5 py-3 text-sm font-semibold text-white hover:bg-cyan-800">
                        Simpan Keputusan
                    </button>
                </div>
            </form>
        @endif
    </div>
</x-app-layout>
