<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Detail operasi</p>
            <h1 class="text-2xl font-bold text-slate-900">{{ $schedule->patient->name }}</h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="flex justify-end">
            <a href="{{ route('doctor.schedules.index') }}" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Kembali
            </a>
        </div>

        <section class="grid gap-6 xl:grid-cols-2">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Jadwal Final</h2>
                <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-2">
                    <div><dt class="text-slate-500">Tanggal</dt><dd class="font-semibold">{{ $schedule->surgery_date?->format('d M Y') }}</dd></div>
                    <div><dt class="text-slate-500">Jam</dt><dd class="font-semibold">{{ $schedule->start_time }} - {{ $schedule->end_time }}</dd></div>
                    <div><dt class="text-slate-500">Kamar</dt><dd class="font-semibold">{{ $schedule->operatingRoom->room_name }}</dd></div>
                    <div><dt class="text-slate-500">Status</dt><dd class="font-semibold">{{ ucfirst($schedule->schedule_status) }}</dd></div>
                </dl>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Pasien dan Tindakan</h2>
                <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-2">
                    <div><dt class="text-slate-500">No RM</dt><dd class="font-semibold">{{ $schedule->patient->medical_record_number }}</dd></div>
                    <div><dt class="text-slate-500">Ruang Asal</dt><dd class="font-semibold">{{ $schedule->patient->origin_room }}</dd></div>
                    <div><dt class="text-slate-500">Diagnosis</dt><dd class="font-semibold">{{ $schedule->surgeryRequest->diagnosis_text ?? '-' }}</dd></div>
                    <div><dt class="text-slate-500">Tindakan</dt><dd class="font-semibold">{{ $schedule->surgeryRequest->procedure_text ?? '-' }}</dd></div>
                </dl>
            </div>
        </section>

        @php($checklist = $schedule->surgeryRequest->preoperativeChecklist)
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold">Kondisi Sebelum Operasi</h2>
            @if ($checklist)
                <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-2 xl:grid-cols-3">
                    <div><dt class="text-slate-500">Consent Bedah</dt><dd class="font-semibold">{{ $checklist->surgical_consent ? 'Ada' : 'Belum ada' }}</dd></div>
                    <div><dt class="text-slate-500">Consent Anestesi</dt><dd class="font-semibold">{{ $checklist->anesthesia_consent ? 'Ada' : 'Belum ada' }}</dd></div>
                    <div><dt class="text-slate-500">Hasil Lab</dt><dd class="font-semibold">{{ $checklist->lab_result_complete ? 'Lengkap' : 'Belum lengkap' }}</dd></div>
                    <div><dt class="text-slate-500">Radiologi</dt><dd class="font-semibold">{{ $checklist->radiology_available ? 'Tersedia' : 'Tidak tersedia' }}</dd></div>
                    <div><dt class="text-slate-500">Konsultasi Anestesi</dt><dd class="font-semibold">{{ $checklist->anesthesia_consultation_done ? 'Sudah' : 'Belum' }}</dd></div>
                    <div><dt class="text-slate-500">Tanda Vital</dt><dd class="font-semibold">{{ $checklist->vital_sign_stable ? 'Stabil' : 'Beresiko' }}</dd></div>
                    <div><dt class="text-slate-500">Tekanan Darah</dt><dd class="font-semibold">{{ $checklist->blood_pressure ?? '-' }}</dd></div>
                    <div><dt class="text-slate-500">Alergi</dt><dd class="font-semibold">{{ $checklist->allergy ?? '-' }}</dd></div>
                    <div><dt class="text-slate-500">Status Puasa</dt><dd class="font-semibold">{{ $checklist->fasting_more_than_6_hours ? 'Lebih dari 6 jam' : 'Kurang dari 6 jam' }}</dd></div>
                </dl>
            @else
                <p class="mt-4 text-sm text-slate-500">Checklist praoperasi belum tersedia.</p>
            @endif
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold">Laporan Operasi</h2>
            <div class="mt-5 space-y-3">
                @forelse ($schedule->operationReports as $report)
                    <div class="rounded-lg bg-slate-50 p-4 text-sm">
                        <p class="font-semibold text-slate-900">{{ ucfirst($report->status) }}</p>
                        <p class="mt-2 text-slate-600">{{ $report->operation_result ?? 'Belum ada hasil operasi.' }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada laporan operasi.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
