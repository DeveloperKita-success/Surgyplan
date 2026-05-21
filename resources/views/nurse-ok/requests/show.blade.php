<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Verifikasi kesiapan operasi sebelum tindakan</p>
            <h1 class="text-3xl font-bold text-slate-950">Verifikasi Data Pasien</h1>
        </div>
    </x-slot>

    @php
        $patient = $surgeryRequest->patient;
        $checklist = $surgeryRequest->preoperativeChecklist;
        $verification = $surgeryRequest->okVerificationChecklist;
        $isReviewable = in_array($surgeryRequest->request_status, ['menunggu', 'ditunda'], true);
        $asaOptions = [
            ['value' => 'ASA I', 'title' => 'ASA I', 'description' => 'Pasien normal, tanpa penyakit sistemik bermakna.'],
            ['value' => 'ASA II', 'title' => 'ASA II', 'description' => 'Penyakit sistemik ringan dan terkontrol.'],
            ['value' => 'ASA III', 'title' => 'ASA III', 'description' => 'Penyakit sistemik berat dengan keterbatasan aktivitas.'],
            ['value' => 'ASA IV', 'title' => 'ASA IV', 'description' => 'Penyakit sistemik berat yang mengancam jiwa.'],
            ['value' => 'ASA V', 'title' => 'ASA V', 'description' => 'Pasien kritis yang tidak diharapkan bertahan tanpa operasi.'],
            ['value' => 'ASA VI', 'title' => 'ASA VI', 'description' => 'Pasien mati batang otak untuk donor organ.'],
            ['value' => 'Emergency', 'title' => 'Emergency', 'description' => 'Tambahan untuk operasi darurat atau emergency.'],
        ];
        $oxygenOptions = [
            '95%-100% (Normal)',
            '90%-94% (Hipoksia ringan / batas bawah)',
            '< 90% (Hipoksia / gawat)',
        ];
        $anesthesiaOptions = [
            'General Anesthesia / Anestesi Umum',
            'Regional Anesthesia / Anestesi Regional',
            'Spinal Anesthesia / Anestesi Spinal',
            'Epidural Anesthesia / Anestesi Epidural',
            'Local Anesthesia / Anestesi Lokal',
            'Sedation / Sedasi',
            'Lainnya',
        ];
        $selectedAnesthesiaType = old('anesthesia_type', in_array($verification?->anesthesia_type, $anesthesiaOptions, true) ? $verification?->anesthesia_type : ($verification?->anesthesia_type ? 'Lainnya' : null));
        $otherAnesthesiaType = old('anesthesia_type_other', $selectedAnesthesiaType === 'Lainnya' ? $verification?->anesthesia_type : '');
        $verificationItems = [
            ['name' => 'patient_wristband_installed', 'title' => 'Gelang pasien sudah terpasang', 'description' => 'Identitas pasien dikonfirmasi dengan benar.', 'checked' => $verification?->patient_wristband_installed],
            ['name' => 'doctor_present', 'title' => 'Kehadiran dokter', 'description' => 'Dokter penanggung jawab telah hadir.', 'checked' => $verification?->doctor_present],
        ];
        $documentItems = [
            ['title' => 'Consent tindakan operasi', 'file' => $checklist?->surgical_consent_file],
            ['title' => 'Consent anestesi bertanda tangan', 'file' => $checklist?->anesthesia_consent_file, 'signature_name' => 'anesthesia_consent_signed', 'checked' => $checklist?->anesthesia_consent_signed],
            ['title' => 'Hasil laboratorium', 'file' => $checklist?->lab_result_file],
            ['title' => 'Hasil radiologi', 'file' => $checklist?->radiology_file],
        ];
    @endphp

    <div class="space-y-5">
        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid gap-5 xl:grid-cols-[1.1fr_0.95fr_0.95fr]">
            <section class="space-y-5">
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="rounded-full bg-emerald-200 px-4 py-2 text-sm font-bold text-emerald-800">
                        DATA PASIEN (Hanya Dapat Dilihat)
                    </div>
                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="grid grid-cols-[130px_12px_1fr] gap-2"><dt class="text-slate-500">No. RM</dt><dd>:</dd><dd class="font-semibold text-slate-800">{{ $patient->medical_record_number }}</dd></div>
                        <div class="grid grid-cols-[130px_12px_1fr] gap-2"><dt class="text-slate-500">Nama Pasien</dt><dd>:</dd><dd class="font-semibold text-slate-800">{{ $patient->name }}</dd></div>
                        <div class="grid grid-cols-[130px_12px_1fr] gap-2"><dt class="text-slate-500">Tanggal Lahir/Umur</dt><dd>:</dd><dd class="font-semibold text-slate-800">{{ $patient->birth_date?->format('d-m-Y') ?? '-' }}{{ $patient->age ? ' ('.$patient->age.' th)' : '' }}</dd></div>
                        <div class="grid grid-cols-[130px_12px_1fr] gap-2"><dt class="text-slate-500">Jenis Kelamin</dt><dd>:</dd><dd class="font-semibold text-slate-800">{{ $patient->gender }}</dd></div>
                        <div class="grid grid-cols-[130px_12px_1fr] gap-2"><dt class="text-slate-500">Diagnosa</dt><dd>:</dd><dd class="font-semibold text-slate-800">{{ $surgeryRequest->diagnosis_text ?? '-' }}</dd></div>
                        <div class="grid grid-cols-[130px_12px_1fr] gap-2"><dt class="text-slate-500">Tindakan</dt><dd>:</dd><dd class="font-semibold text-slate-800">{{ $surgeryRequest->procedure_text ?? '-' }}</dd></div>
                        <div class="grid grid-cols-[130px_12px_1fr] gap-2"><dt class="text-slate-500">DPJ</dt><dd>:</dd><dd class="font-semibold text-slate-800">{{ $surgeryRequest->requestedDoctor?->title }} {{ $surgeryRequest->requestedDoctor?->user?->name ?? '-' }}</dd></div>
                        <div class="grid grid-cols-[130px_12px_1fr] gap-2"><dt class="text-slate-500">Ruang Asal</dt><dd>:</dd><dd class="font-semibold text-slate-800">{{ $patient->origin_room ?? '-' }}</dd></div>
                    </dl>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="rounded-full bg-emerald-200 px-4 py-2 text-sm font-bold text-emerald-800">
                        Informed Consent
                    </div>
                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="grid grid-cols-[140px_12px_1fr] gap-2"><dt class="text-slate-500">Persetujuan Tindakan</dt><dd>:</dd><dd class="font-semibold text-slate-800">{{ $checklist?->surgical_consent ? 'Ada' : 'Belum ada' }}</dd></div>
                        <div class="grid grid-cols-[140px_12px_1fr] gap-2"><dt class="text-slate-500">Persetujuan Anestesi</dt><dd>:</dd><dd class="font-semibold text-slate-800">{{ $checklist?->anesthesia_consent ? 'Ada' : 'Belum ada' }}</dd></div>
                        <div class="grid grid-cols-[140px_12px_1fr] gap-2"><dt class="text-slate-500">TTD Anestesi</dt><dd>:</dd><dd class="font-semibold text-slate-800">{{ $checklist?->anesthesia_consent_signed ? 'Sudah TTD' : 'Belum TTD' }}</dd></div>
                        <div class="grid grid-cols-[140px_12px_1fr] gap-2"><dt class="text-slate-500">Alergi</dt><dd>:</dd><dd class="font-semibold text-slate-800">{{ $checklist?->allergy ?: 'Tidak ada' }}</dd></div>
                        <div class="grid grid-cols-[140px_12px_1fr] gap-2"><dt class="text-slate-500">Catatan tambahan</dt><dd>:</dd><dd class="font-semibold text-slate-800">{{ $checklist?->final_note ?: '-' }}</dd></div>
                    </dl>
                    <div class="mt-4 space-y-2">

                    </div>
                </div>
            </section>

            @if ($isReviewable)
                <form id="verification-form" method="POST" action="{{ route('nurse-ok.requests.decide', $surgeryRequest) }}" class="xl:col-span-2">
                    @csrf
                    <div class="grid gap-5 xl:grid-cols-2">
                        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div class="rounded-full bg-emerald-200 px-4 py-2 text-sm font-bold text-emerald-800">
                                Checklist Verifikasi
                            </div>

                            <div class="mt-4 space-y-3">
                                @foreach ($documentItems as $item)
                                    <div class="flex items-center justify-between gap-3 rounded-lg border border-emerald-100 bg-emerald-50 px-3 py-2 text-sm">
                                        <div>
                                            <p class="font-semibold text-slate-800">{{ $item['title'] }}</p>
                                            <p class="text-xs text-slate-500">{{ $item['file'] ? 'File tersedia' : 'File belum tersedia' }}</p>
                                        </div>

                                        <div class="flex items-center gap-3">
                                            @if ($item['file'])
                                                <a href="{{ asset('storage/'.$item['file']) }}" target="_blank" class="rounded-md bg-emerald-700 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-800">
                                                    Lihat File
                                                </a>
                                            @endif

                                            @isset($item['signature_name'])
                                                @if ($isReviewable)
                                                    <input type="hidden" name="{{ $item['signature_name'] }}" value="0" form="verification-form">
                                                    <label class="inline-flex items-center gap-2 text-xs font-bold text-emerald-800">
                                                        <input
                                                            type="checkbox"
                                                            name="{{ $item['signature_name'] }}"
                                                            value="1"
                                                            form="verification-form"
                                                            @checked(old($item['signature_name'], $item['checked']))
                                                            class="h-5 w-5 rounded border-slate-300 text-cyan-700 focus:ring-cyan-600"
                                                        >
                                                        Sudah TTD
                                                    </label>
                                                @else
                                                    <span class="rounded-md px-2.5 py-1 text-xs font-semibold {{ $item['checked'] ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                                        {{ $item['checked'] ? 'Sudah TTD' : 'Belum TTD' }}
                                                    </span>
                                                @endif
                                            @endisset
                                        </div>
                                    </div>
                                @endforeach
                                @foreach ($verificationItems as $item)
                                    <div class="rounded-lg bg-emerald-100 p-3">
                                        <span>
                                            <span class="block text-sm font-bold text-emerald-900">{{ $item['title'] }}</span>
                                            <span class="block text-xs text-emerald-800">{{ $item['description'] }}</span>
                                        </span>
                                        <div class="mt-3 flex gap-3 text-sm font-semibold text-emerald-900">
                                            <label class="inline-flex items-center gap-2">
                                                <input type="radio" name="{{ $item['name'] }}" value="1" @checked((string) old($item['name'], (int) ($item['checked'] ?? 0)) === '1') class="border-slate-300 text-cyan-700 focus:ring-cyan-600">
                                                Iya
                                            </label>
                                            <label class="inline-flex items-center gap-2">
                                                <input type="radio" name="{{ $item['name'] }}" value="0" @checked((string) old($item['name'], (int) ($item['checked'] ?? 0)) === '0') class="border-slate-300 text-cyan-700 focus:ring-cyan-600">
                                                Tidak
                                            </label>
                                        </div>
                                    </div>
                                @endforeach

                                <label class="block rounded-lg bg-emerald-100 p-3">
                                    <span class="block text-sm font-bold text-emerald-900">Saturasi Oksigen</span>
                                    <select name="oxygen_saturation" class="mt-2 w-full rounded-lg border-emerald-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                                        @foreach ($oxygenOptions as $oxygenOption)
                                            <option value="{{ $oxygenOption }}" @selected(old('oxygen_saturation', $verification?->oxygen_saturation) === $oxygenOption)>{{ $oxygenOption }}</option>
                                        @endforeach
                                    </select>
                                </label>

                                <div class="grid gap-3 md:grid-cols-2">
                                    <label class="block rounded-lg bg-emerald-100 p-3">
                                        <span class="block text-sm font-bold text-emerald-900">Nama Dokter anestesi</span>
                                        <input name="anesthesiologist_name" value="{{ old('anesthesiologist_name', $verification?->anesthesiologist_name) }}" class="mt-2 w-full rounded-lg border-emerald-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                                    </label>
                                    <div
                                        x-data="{ anesthesiaType: @js($selectedAnesthesiaType) }"
                                        class="rounded-lg bg-emerald-100 p-3 md:col-span-2"
                                    >
                                        <span class="block text-sm font-bold text-emerald-900">Jenis Anestesi</span>
                                        <div class="mt-3 grid gap-2 text-sm text-emerald-950 md:grid-cols-2">
                                            @foreach ($anesthesiaOptions as $anesthesiaOption)
                                                <label class="inline-flex items-center gap-2">
                                                    <input
                                                        type="radio"
                                                        name="anesthesia_type"
                                                        value="{{ $anesthesiaOption }}"
                                                        x-model="anesthesiaType"
                                                        @checked($selectedAnesthesiaType === $anesthesiaOption)
                                                        class="border-slate-300 text-cyan-700 focus:ring-cyan-600"
                                                    >
                                                    {{ $anesthesiaOption }}
                                                </label>
                                            @endforeach
                                        </div>

                                        <div x-show="anesthesiaType === 'Lainnya'" x-cloak class="mt-3">
                                            <label class="text-sm font-semibold text-emerald-900">Jenis anestesi lainnya</label>
                                            <input
                                                name="anesthesia_type_other"
                                                value="{{ $otherAnesthesiaType }}"
                                                class="mt-2 w-full rounded-lg border-emerald-200 text-sm focus:border-cyan-600 focus:ring-cyan-600"
                                            >
                                        </div>
                                    </div>
                                </div>

                                <label class="block rounded-lg bg-emerald-100 p-3">
                                    <span class="block text-sm font-bold text-emerald-900">Catatan anestesi</span>
                                    <textarea name="anesthesia_note" rows="2" class="mt-2 w-full rounded-lg border-emerald-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">{{ old('anesthesia_note', $verification?->anesthesia_note) }}</textarea>
                                </label>
                            </div>
                        </section>

                        <section class="rounded-2xl border border-cyan-500 bg-white p-4 shadow-sm">
                            <div class="rounded-full bg-emerald-200 px-4 py-2 text-sm font-bold text-emerald-800">
                                Status Persetujuan Anestesi
                            </div>
                            <p class="mt-1 text-xs text-slate-500">Disetujui oleh dokter anestesi.</p>

                            <div class="mt-4 overflow-hidden rounded-lg border border-emerald-200">
                                <div class="grid grid-cols-[64px_90px_1fr] bg-emerald-300 px-3 py-2 text-xs font-bold text-emerald-900">
                                    <span>Pilih</span>
                                    <span>Sub Bab</span>
                                    <span>Penjelasan</span>
                                </div>
                                <div class="divide-y divide-emerald-100">
                                    @foreach ($asaOptions as $option)
                                        <label class="grid grid-cols-[64px_90px_1fr] items-center gap-2 bg-emerald-100 px-3 py-3 text-xs hover:bg-emerald-200">
                                            <input type="radio" name="asa_status" value="{{ $option['value'] }}" @checked(old('asa_status', $verification?->asa_status) === $option['value']) class="h-4 w-4 border-slate-300 text-cyan-700 focus:ring-cyan-600">
                                            <span class="font-bold text-emerald-900">{{ $option['title'] }}</span>
                                            <span class="text-emerald-900">{{ $option['description'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mt-4 rounded-lg border border-slate-200 p-3">
                                <label class="text-sm font-bold text-slate-700">Pilih Dokter Operator</label>
                                <select name="doctor_id" class="mt-2 w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                                    <option value="">Pilih dokter</option>
                                    @foreach ($doctors as $doctor)
                                        <option value="{{ $doctor->id }}" @selected(old('doctor_id', $surgeryRequest->requested_doctor_id) == $doctor->id)>
                                            {{ $doctor->title }} {{ $doctor->user?->name }}{{ $doctor->specialist ? ' - '.$doctor->specialist->name : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mt-4 rounded-lg border border-slate-200 p-3">
                                <label class="text-sm font-bold text-slate-700">Pilih Kamar / Ruang Operasi</label>
                                <select name="operating_room_id" class="mt-2 w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                                    <option value="">Pilih kamar operasi</option>
                                    @foreach ($rooms as $room)
                                        @php($isBooked = $room->surgerySchedules->isNotEmpty())
                                        <option value="{{ $room->id }}" @selected(old('operating_room_id') == $room->id) @disabled($isBooked || $room->status !== 'siap')>
                                            {{ $room->room_name }} - {{ $room->room_code }}{{ $room->specialist ? ' ('.$room->specialist->name.')' : '' }}{{ $isBooked ? ' - sudah terpakai tanggal ini' : '' }}{{ $room->status !== 'siap' ? ' - '.$room->status : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mt-4 grid gap-3 md:grid-cols-2">
                                <label class="block">
                                    <span class="text-sm font-bold text-slate-700">Jam mulai</span>
                                    <input type="time" value="{{ substr((string) $surgeryRequest->requested_start_time, 0, 5) }}" disabled class="mt-2 w-full rounded-lg border-slate-200 bg-slate-50 text-sm text-slate-500">
                                </label>
                                <label class="block">
                                    <span class="text-sm font-bold text-slate-700">Jam selesai</span>
                                    <input type="time" name="end_time" value="{{ old('end_time', substr((string) $surgeryRequest->requested_end_time, 0, 5)) }}" class="mt-2 w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                                </label>
                            </div>
                        </section>
                    </div>

                    <section class="mt-5 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="rounded-full bg-emerald-200 px-4 py-2 text-sm font-bold text-emerald-800">
                            Status Akhir (pilih salah satu)
                        </div>
                            <div class="mt-3 grid gap-3 md:grid-cols-3">
                                <label class="rounded-lg border border-emerald-200 bg-emerald-50 p-3">
                                    <input type="radio" name="decision" value="disetujui" @checked(old('decision', 'disetujui') === 'disetujui') class="border-slate-300 text-cyan-700 focus:ring-cyan-600">
                                    <span class="ml-2 font-bold text-emerald-700">Diterima</span>
                                    <p class="mt-1 text-xs text-emerald-700">Ruangan operasi siap dan pasien bisa dijadwalkan.</p>
                                </label>
                            <label class="rounded-lg border border-amber-200 bg-amber-50 p-3">
                                <input type="radio" name="decision" value="ditunda" @checked(old('decision') === 'ditunda') class="border-slate-300 text-cyan-700 focus:ring-cyan-600">
                                <span class="ml-2 font-bold text-amber-700">Jadwalkan Ulang</span>
                                <p class="mt-1 text-xs text-amber-700">Operasi dijadwalkan ulang pada waktu yang ditentukan.</p>
                            </label>
                            <label class="rounded-lg border border-rose-200 bg-rose-50 p-3">
                                <input type="radio" name="decision" value="ditolak" @checked(old('decision') === 'ditolak') class="border-slate-300 text-cyan-700 focus:ring-cyan-600">
                                <span class="ml-2 font-bold text-rose-700">Ditolak</span>
                                <p class="mt-1 text-xs text-rose-700">Operasi ditolak untuk sementara waktu.</p>
                            </label>
                        </div>

                        <label class="mt-3 block">
                            <span class="text-sm font-semibold text-slate-700">Catatan Verifikasi</span>
                            <textarea name="verification_note" rows="2" maxlength="200" placeholder="Tulis catatan di sini..." class="mt-2 w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">{{ old('verification_note', $verification?->verification_note) }}</textarea>
                        </label>

                        <label class="mt-3 block">
                            <span class="text-sm font-semibold text-slate-700">Alasan jika ditolak / dijadwalkan ulang</span>
                            <input name="reason" value="{{ old('reason') }}" class="mt-2 w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                        </label>

                        <div class="mt-4 flex justify-end">
                            <button type="submit" class="rounded-lg bg-blue-700 px-5 py-3 text-sm font-bold text-white hover:bg-blue-800">
                                Simpan Verifikasi
                            </button>
                        </div>
                    </section>
                </form>
            @else
                <section class="xl:col-span-2 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-bold text-slate-900">Hasil Verifikasi</h2>
                    <p class="mt-2 text-sm text-slate-600">Pengajuan ini sudah memiliki status akhir: <span class="font-bold">{{ ucfirst($surgeryRequest->request_status) }}</span>.</p>
                    <dl class="mt-5 grid gap-4 text-sm md:grid-cols-2">
                        <div><dt class="text-slate-500">ASA</dt><dd class="font-semibold text-slate-900">{{ $verification?->asa_status ?? '-' }}</dd></div>
                        <div><dt class="text-slate-500">Dokter Anestesi</dt><dd class="font-semibold text-slate-900">{{ $verification?->anesthesiologist_name ?? '-' }}</dd></div>
                        <div><dt class="text-slate-500">Jenis Anestesi</dt><dd class="font-semibold text-slate-900">{{ $verification?->anesthesia_type ?? '-' }}</dd></div>
                        <div><dt class="text-slate-500">Catatan</dt><dd class="font-semibold text-slate-900">{{ $verification?->verification_note ?? '-' }}</dd></div>
                    </dl>
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
