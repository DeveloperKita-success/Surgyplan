<x-app-layout>
    <x-slot name="header">
        <div class="space-y-2">
            <p class="text-sm font-medium text-slate-500">Alur perawat biasa</p>
            <h1 class="text-2xl font-bold text-slate-900">Request Room Operation</h1>
            <p class="max-w-3xl text-sm text-slate-500">
                Form ini dipakai oleh perawat biasa untuk mengisi biodata pasien, diagnosis, tindakan, dan checklist persiapan operasi.
            </p>
        </div>
    </x-slot>

    @php
        $selectedPriority = old('patient_priority', 'Elektif');
        $statusStyles = [
            'Imminent' => 'border-rose-200 bg-rose-50 text-rose-700',
            'Cito' => 'border-orange-200 bg-orange-50 text-orange-700',
            'Urgent' => 'border-yellow-200 bg-yellow-50 text-yellow-700',
            'Expedited' => 'border-sky-200 bg-sky-50 text-sky-700',
            'Elektif' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
        ];
        $binaryYesNo = [
            '1' => 'Ya',
            '0' => 'Tidak',
        ];
    @endphp

    <div class="grid gap-6 xl:grid-cols-[1.6fr_0.9fr]">
        <div class="space-y-6">
            @if (session('status'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                    Ada data yang belum lengkap. Periksa kembali kolom yang ditandai.
                </div>
            @endif

            <form action="{{ route('nurse.regular.room-operation.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Identitas Pasien</h2>
                            <p class="mt-1 text-sm text-slate-500">Lengkapi No RM dan biodata dasar pasien.</p>
                        </div>
                        <span class="rounded-full bg-cyan-50 px-3 py-1 text-xs font-semibold text-cyan-700">Langkah 1</span>
                    </div>

                    <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">No RM</span>
                            <input type="text" name="medical_record_number" value="{{ old('medical_record_number') }}" class="w-full rounded-[10px] border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600" placeholder="Masukkan nomor rekam medis">
                            @error('medical_record_number')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                        </label>

                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Nama Pasien</span>
                            <input type="text" name="patient_name" value="{{ old('patient_name') }}" class="w-full rounded-[10px] border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600" placeholder="Nama lengkap pasien">
                            @error('patient_name')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                        </label>

                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Tanggal Lahir</span>
                            <input id="birth_date" type="date" name="birth_date" value="{{ old('birth_date') }}" class="w-full rounded-[10px] border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                            @error('birth_date')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                        </label>

                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Umur</span>
                            <input id="age_display" type="text" value="{{ old('age') }}" readonly class="w-full rounded-[10px] border-slate-200 bg-slate-50 text-sm text-slate-600 focus:border-cyan-600 focus:ring-cyan-600" placeholder="Akan dihitung otomatis">
                            <p class="text-xs text-slate-500">Umur dihitung otomatis dari tanggal lahir.</p>
                        </label>

                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Jenis Kelamin</span>
                            <select name="gender" class="w-full rounded-[10px] border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                                <option value="">Pilih jenis kelamin</option>
                                @foreach (['Laki-laki', 'Perempuan'] as $gender)
                                    <option value="{{ $gender }}" @selected(old('gender') === $gender)>{{ $gender }}</option>
                                @endforeach
                            </select>
                            @error('gender')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                        </label>

                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Ruang Asal</span>
                            <select name="origin_room" class="w-full rounded-[10px] border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                                <option value="">Pilih ruang asal</option>
                                @foreach ($originRooms as $room)
                                    <option value="{{ $room }}" @selected(old('origin_room') === $room)>{{ $room }}</option>
                                @endforeach
                            </select>
                            @error('origin_room')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                        </label>

                        <label class="space-y-2 md:col-span-2 xl:col-span-3">
                            <span class="text-sm font-semibold text-slate-700">Dokter Tujuan</span>
                            <select name="requested_doctor_id" class="w-full rounded-[10px] border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                                <option value="">Pilih dokter (opsional)</option>
                                @foreach ($doctors as $doctor)
                                    <option value="{{ $doctor->id }}" @selected((string) old('requested_doctor_id') === (string) $doctor->id)>
                                        {{ $doctor->user?->name ?? 'Dokter' }}{{ $doctor->specialist?->name ? ' - ' . $doctor->specialist->name : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('requested_doctor_id')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                        </label>

                        <label class="space-y-2 md:col-span-2 xl:col-span-3">
                            <span class="text-sm font-semibold text-slate-700">Alamat / Keterangan Singkat</span>
                            <textarea name="address" rows="2" class="w-full rounded-[10px] border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600" placeholder="Alamat atau keterangan tambahan">{{ old('address') }}</textarea>
                            @error('address')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                        </label>

                        <div class="grid gap-4 md:col-span-2 md:grid-cols-2 xl:col-span-3">
                            <label class="space-y-2">
                                <span class="text-sm font-semibold text-slate-700">Nomor Telepon</span>
                                <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-[10px] border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600" placeholder="08xxxxxxxxxx">
                                @error('phone')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                            </label>

                            <label class="space-y-2">
                                <span class="text-sm font-semibold text-slate-700">Tanggal Pengajuan</span>
                                <input type="date" name="requested_date" value="{{ old('requested_date', now()->format('Y-m-d')) }}" class="w-full rounded-[10px] border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                                @error('requested_date')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                            </label>

                            <label class="space-y-2">
                                <span class="text-sm font-semibold text-slate-700">Waktu Mulai</span>
                                <input type="time" name="requested_start_time" value="{{ old('requested_start_time') }}" class="w-full rounded-[10px] border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                                @error('requested_start_time')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                            </label>

                            <label class="space-y-2">
                                <span class="text-sm font-semibold text-slate-700">Waktu Selesai</span>
                                <input type="time" name="requested_end_time" value="{{ old('requested_end_time') }}" class="w-full rounded-[10px] border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                                @error('requested_end_time')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                            </label>
                        </div>

                    </div>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Diagnosis dan Tindakan</h2>
                        <p class="mt-1 text-sm text-slate-500">Tulis diagnosis ICD-10 dan tindakan ICD-9, lalu gunakan pedoman sebagai referensi.</p>
                    </div>

                    <div class="mt-6 grid gap-4 lg:grid-cols-[1.2fr_1fr]">
                        <div class="grid gap-4">
                            <label class="space-y-2">
                                <span class="text-sm font-semibold text-slate-700">Diagnosa </span>
                                <input type="text" name="diagnosis_text" value="{{ old('diagnosis_text') }}" class="w-full rounded-[10px] border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600" placeholder="Contoh: K35.8 - Appendicitis akut">
                                @error('diagnosis_text')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                            </label>

                            <label class="space-y-2">
                                <span class="text-sm font-semibold text-slate-700">Tindakan </span>
                                <input type="text" name="procedure_text" value="{{ old('procedure_text') }}" class="w-full rounded-[10px] border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600" placeholder="Contoh: 47.01 - Appendektomi">
                                @error('procedure_text')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                            </label>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm font-semibold text-slate-700">Buku pedoman ICD</p>
                            <p class="mt-1 text-xs text-slate-500">Gunakan dokumen ini jika perlu Bantuan.</p>

                            <div class="mt-4 space-y-3 text-sm">
                                <div class="rounded-xl border border-slate-200 bg-white p-3">
                                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">ICD-10</p>
                                    <div class="mt-2 space-y-2">
                                        @forelse ($icd10Guidelines as $guideline)
                                            @if ($guideline->file)
                                                <a href="{{ Storage::url($guideline->file) }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between rounded-lg border border-cyan-200 bg-cyan-50 px-3 py-2 text-sm font-semibold text-cyan-700 hover:bg-cyan-100">
                                                    <span>{{ $guideline->title }}</span>
                                                    <span>PDF</span>
                                                </a>
                                            @endif
                                        @empty
                                            <a href="{{ route('guidelines.index', ['type' => 'ICD-10']) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-cyan-700 hover:text-cyan-800">Lihat pedoman ICD-10</a>
                                        @endforelse
                                    </div>
                                </div>

                                <div class="rounded-xl border border-slate-200 bg-white p-3">
                                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">ICD-9</p>
                                    <div class="mt-2 space-y-2">
                                        @forelse ($icd9Guidelines as $guideline)
                                            @if ($guideline->file)
                                                <a href="{{ Storage::url($guideline->file) }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between rounded-lg border border-cyan-200 bg-cyan-50 px-3 py-2 text-sm font-semibold text-cyan-700 hover:bg-cyan-100">
                                                    <span>{{ $guideline->title }}</span>
                                                    <span>PDF</span>
                                                </a>
                                            @endif
                                        @empty
                                            <a href="{{ route('guidelines.index', ['type' => 'ICD-9']) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-cyan-700 hover:text-cyan-800">Lihat pedoman ICD-9</a>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Prioritas Pasien</h2>
                        <p class="mt-1 text-sm text-slate-500">Klik salah satu kartu prioritas untuk memilih tingkat urgensi pasien.</p>
                    </div>

                    <div class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                        @foreach ($statusOptions as $statusOption)
                            @php $isSelected = $selectedPriority === $statusOption['value']; @endphp
                            <label class="group cursor-pointer">
                                <input type="radio" name="patient_priority" value="{{ $statusOption['value'] }}" class="peer sr-only" @checked($isSelected)>
                                <div class="flex min-h-28 flex-col justify-between rounded-2xl border p-4 shadow-sm transition-all duration-200 ease-out {{ $statusStyles[$statusOption['value']] }} hover:-translate-y-1 hover:shadow-md peer-checked:-translate-y-1 peer-checked:scale-[1.02] peer-checked:ring-2 peer-checked:ring-offset-2 peer-checked:ring-offset-white peer-checked:shadow-xl">
                                    <div class="flex items-start justify-between gap-3">
                                        <p class="text-sm font-semibold tracking-wide">{{ $statusOption['label'] }}</p>
                                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-white/80 text-[11px] font-bold text-slate-700 shadow-sm transition-transform duration-200 peer-checked:scale-110">
                                            {{ str_pad((string) ($loop->iteration), 2, '0', STR_PAD_LEFT) }}
                                        </span>
                                    </div>
                                    <div class="mt-3 flex items-center justify-between text-xs font-semibold uppercase tracking-[0.18em] opacity-80">
                                        <span class="flex items-center gap-2">
                                            <span class="h-2.5 w-2.5 rounded-full bg-current"></span>
                                            Pilih
                                        </span>
                                        <span class="transition-opacity duration-200 {{ $isSelected ? 'opacity-100' : 'opacity-0 group-hover:opacity-100' }}">Aktif</span>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('patient_priority')<p class="mt-3 text-xs text-rose-600">{{ $message }}</p>@enderror
                </section>

                <div id="upload-notification" class="hidden rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-700 shadow-lg transition-all duration-300"></div>

                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Checklist Persiapan Operasi</h2>
                        <p class="mt-1 text-sm text-slate-500">Gunakan checkbox dan radio untuk mengisi status persiapan pasien sebelum operasi.</p>
                    </div>

                    <div class="mt-6 space-y-5">
                        <div class="mt-6 grid gap-4 md:grid-cols-2">
                            <div class="rounded-2xl border border-slate-200 p-4">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="font-semibold text-slate-900">Informed consent bedah</p>
                                        <p class="text-sm text-slate-500">Checklist dan upload dokumen.</p>
                                    </div>
                                    <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 shrink-0">
                                        <input type="hidden" name="surgical_consent" value="0">
                                        <input type="checkbox" name="surgical_consent" value="1" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-600" data-toggle-target="surgical_consent_file" @checked(old('surgical_consent'))>
                                        Tersedia
                                    </label>
                                </div>
                                <div class="relative" data-file-wrapper="surgical_consent_file">
                                    <input type="file" name="surgical_consent_file" data-toggle-id="surgical_consent_file" class="mt-4 w-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-3 py-2 text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-cyan-600 file:px-3 file:py-2 file:text-white hover:file:bg-cyan-700 disabled:pointer-events-none" {{ old('surgical_consent') ? '' : 'disabled' }}>
                                </div>
                                @error('surgical_consent_file')<p class="mt-2 text-xs text-rose-600">{{ $message }}</p>@enderror
                            </div>

                            <div class="rounded-2xl border border-slate-200 p-4">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="font-semibold text-slate-900">Informed consent anestesi</p>
                                        <p class="text-sm text-slate-500">Checklist dan upload dokumen.</p>
                                    </div>
                                            <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 shrink-0">
                                        <input type="hidden" name="anesthesia_consent" value="0">
                                        <input type="checkbox" name="anesthesia_consent" value="1" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-600" data-toggle-target="anesthesia_consent_file" @checked(old('anesthesia_consent'))>
                                                Tersedia
                                    </label>
                                </div>
                                <div class="relative" data-file-wrapper="anesthesia_consent_file">
                                    <input type="file" name="anesthesia_consent_file" data-toggle-id="anesthesia_consent_file" class="mt-4 w-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-3 py-2 text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-cyan-600 file:px-3 file:py-2 file:text-white hover:file:bg-cyan-700 disabled:pointer-events-none" {{ old('anesthesia_consent') ? '' : 'disabled' }}>
                                </div>
                                @error('anesthesia_consent_file')<p class="mt-2 text-xs text-rose-600">{{ $message }}</p>@enderror
                            </div>

                            <div class="rounded-2xl border border-slate-200 p-4">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="font-semibold text-slate-900">Hasil lab lengkap</p>
                                        <p class="text-sm text-slate-500">Checklist dan upload hasil lab.</p>
                                    </div>
                                    <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 shrink-0">
                                        <input type="hidden" name="lab_result_complete" value="0">
                                        <input type="checkbox" name="lab_result_complete" value="1" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-600" data-toggle-target="lab_result_file" @checked(old('lab_result_complete'))>
                                        Tersedia
                                    </label>
                                </div>
                                <div class="relative" data-file-wrapper="lab_result_file">
                                    <input type="file" name="lab_result_file" data-toggle-id="lab_result_file" class="mt-4 w-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-3 py-2 text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-cyan-600 file:px-3 file:py-2 file:text-white hover:file:bg-cyan-700 disabled:pointer-events-none" {{ old('lab_result_complete') ? '' : 'disabled' }}>
                                </div>
                                @error('lab_result_file')<p class="mt-2 text-xs text-rose-600">{{ $message }}</p>@enderror
                            </div>

                            <div class="rounded-2xl border border-slate-200 p-4">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="font-semibold text-slate-900">Hasil radiologi</p>
                                        <p class="text-sm text-slate-500">Checklist ketersediaan hasil radiologi.</p>
                                    </div>
                                    <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 shrink-0">
                                        <input type="hidden" name="radiology_available" value="0">
                                        <input type="checkbox" name="radiology_available" value="1" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-600" data-toggle-target="radiology_file" @checked(old('radiology_available'))>
                                        Tersedia
                                    </label>
                                </div>
                                <div class="relative" data-file-wrapper="radiology_file">
                                    <input type="file" name="radiology_file" data-toggle-id="radiology_file" class="mt-4 w-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-3 py-2 text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-cyan-600 file:px-3 file:py-2 file:text-white hover:file:bg-cyan-700 disabled:pointer-events-none" {{ old('radiology_available') ? '' : 'disabled' }}>
                                </div>
                                @error('radiology_file')<p class="mt-2 text-xs text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="rounded-2xl border border-slate-200 p-4">
                                <p class="font-semibold text-slate-900">Konsultasi anestesi</p>
                                <div class="mt-3 flex flex-wrap items-center gap-2 text-sm font-medium text-slate-700">
                                    <input type="checkbox" name="anesthesia_consultation_done" value="Sudah dikonsultasikan" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-600" @checked(old('anesthesia_consultation_done') === 'Sudah dikonsultasikan')>
                                    <span>Sudah dikonsultasikan</span>
                                </div>
                            </div>

                            <label class="space-y-2 rounded-2xl border border-slate-200 p-4">
                                <span class="text-sm font-semibold text-slate-700">Estimasi resiko anestesi</span>
                                <input type="text" name="anesthesia_risk_estimation" value="{{ old('anesthesia_risk_estimation') }}" class="w-full rounded-[10px] border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600" placeholder="Contoh: ASA II, risiko sedang">
                                @error('anesthesia_risk_estimation')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                            </label>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="rounded-2xl border border-slate-200 p-4">
                                <p class="font-semibold text-slate-900">Tanda vital stabil</p>
                                <input type="hidden" id="vital_sign_stable_value" name="vital_sign_stable" value="{{ old('vital_sign_stable', 'Beresiko') }}">
                                <div class="mt-3 flex flex-wrap items-center gap-4 text-sm font-medium text-slate-700">
                                    <label class="inline-flex items-center gap-2">
                                        <input type="checkbox" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-600" data-vital-option="Beresiko" {{ old('vital_sign_stable', 'Beresiko') === 'Beresiko' ? 'checked' : '' }}>
                                        <span>Beresiko</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2">
                                        <input type="checkbox" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-600" data-vital-option="Stabil" {{ old('vital_sign_stable') === 'Stabil' ? 'checked' : '' }}>
                                        <span>Stabil</span>
                                    </label>
                                </div>
                                <div id="vital_sign_note_wrapper" class="mt-4 {{ old('vital_sign_stable') === 'Stabil' ? '' : 'hidden' }}">
                                    <input type="text" name="vital_sign_note" value="{{ old('vital_sign_note') }}" class="w-full rounded-[10px] border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600" placeholder="Contoh stabil: TD 120/80, Nadi 84, RR 18">
                                    @error('vital_sign_note')<p class="mt-2 text-xs text-rose-600">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <label class="space-y-2 rounded-2xl border border-slate-200 p-4">
                                <span class="text-sm font-semibold text-slate-700">Status pasien tekanan darah</span>
                                <input type="text" name="blood_pressure" value="{{ old('blood_pressure') }}" class="w-full rounded-[10px] border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600" placeholder="Contoh: 120/80 mmHg">
                                @error('blood_pressure')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                            </label>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="space-y-2 rounded-2xl border border-slate-200 p-4">
                                <span class="text-sm font-semibold text-slate-700">Alergi pasien</span>
                                <input type="text" name="allergy" value="{{ old('allergy') }}" class="w-full rounded-[10px] border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600" placeholder="Contoh: Tidak ada / obat tertentu">
                                @error('allergy')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                            </label>

                            <div class="rounded-2xl border border-slate-200 p-4">
                                <p class="font-semibold text-slate-900">Status puasa</p>
                                <div class="mt-3 grid gap-2 text-sm font-medium text-slate-700">
                                    <label class="inline-flex items-center gap-2">
                                        <input type="radio" name="fasting_more_than_6_hours" value="Kurang dari 6 jam" class="text-cyan-600 focus:ring-cyan-600" @checked(old('fasting_more_than_6_hours') === 'Kurang dari 6 jam')>
                                        <span>Kurang dari 6 jam</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2">
                                        <input type="radio" name="fasting_more_than_6_hours" value="Lebih dari 6 jam" class="text-cyan-600 focus:ring-cyan-600" @checked(old('fasting_more_than_6_hours') === 'Lebih dari 6 jam')>
                                        <span>Lebih dari 6 jam</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="grid gap-4">
                            <label class="space-y-2">
                                <span class="text-sm font-semibold text-slate-700">Golongan darah</span>
                                <input type="text" name="blood_type" value="{{ old('blood_type') }}" class="w-full rounded-[10px] border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600" placeholder="A / B / AB / O">
                                @error('blood_type')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                            </label>

                            <div class="rounded-2xl border border-slate-200 p-4">
                                <p class="font-semibold text-slate-900">Darah tersedia</p>
                                <div class="mt-3 grid gap-2 text-sm font-medium text-slate-700">
                                    <label class="inline-flex items-center gap-2">
                                        <input type="radio" name="blood_available" value="Tersedia" class="text-cyan-600 focus:ring-cyan-600" @checked(old('blood_available') === 'Tersedia')>
                                        <span>Tersedia</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2">
                                        <input type="radio" name="blood_available" value="Tidak tersedia" class="text-cyan-600 focus:ring-cyan-600" @checked(old('blood_available') === 'Tidak tersedia')>
                                        <span>Tidak tersedia</span>
                                    </label>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 p-4">
                                <p class="font-semibold text-slate-900">Infus terpasang</p>
                                <div class="mt-3 grid gap-2 text-sm font-medium text-slate-700">
                                    <label class="inline-flex items-center gap-2">
                                        <input type="radio" name="infusion_installed" value="Terpasang" class="text-cyan-600 focus:ring-cyan-600" @checked(old('infusion_installed') === 'Terpasang')>
                                        <span>Terpasang</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2">
                                        <input type="radio" name="infusion_installed" value="Tidak terpasang" class="text-cyan-600 focus:ring-cyan-600" @checked(old('infusion_installed') === 'Tidak terpasang')>
                                        <span>Tidak terpasang</span>
                                    </label>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 p-4">
                                <p class="font-semibold text-slate-900">Kateter</p>
                                <div class="mt-3 grid gap-2 text-sm font-medium text-slate-700">
                                    <label class="inline-flex items-center gap-2">
                                        <input type="radio" name="catheter_installed" value="Terpasang" class="text-cyan-600 focus:ring-cyan-600" @checked(old('catheter_installed') === 'Terpasang')>
                                        <span>Terpasang</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2">
                                        <input type="radio" name="catheter_installed" value="Tidak terpasang" class="text-cyan-600 focus:ring-cyan-600" @checked(old('catheter_installed') === 'Tidak terpasang')>
                                        <span>Tidak terpasang</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="grid gap-4">
                            <div class="rounded-2xl border border-slate-200 p-4">
                                <p class="font-semibold text-slate-900">Pencukuran area operasi</p>
                                <div class="mt-3 grid gap-2 text-sm font-medium text-slate-700">
                                    <label class="inline-flex items-center gap-2">
                                        <input type="radio" name="surgical_area_shaved" value="Belum dilakukan" class="text-cyan-600 focus:ring-cyan-600" @checked(old('surgical_area_shaved') === 'Belum dilakukan')>
                                        <span>Belum dilakukan</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2">
                                        <input type="radio" name="surgical_area_shaved" value="Sudah dilakukan" class="text-cyan-600 focus:ring-cyan-600" @checked(old('surgical_area_shaved') === 'Sudah dilakukan')>
                                        <span>Sudah dilakukan</span>
                                    </label>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 p-4">
                                <p class="font-semibold text-slate-900">Pelepasan perhiasan</p>
                                <div class="mt-3 grid gap-2 text-sm font-medium text-slate-700">
                                    <label class="inline-flex items-center gap-2">
                                        <input type="radio" name="jewelry_removed" value="Terlepas" class="text-cyan-600 focus:ring-cyan-600" @checked(old('jewelry_removed') === 'Terlepas')>
                                        <span>Terlepas</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2">
                                        <input type="radio" name="jewelry_removed" value="Tidak terlepas" class="text-cyan-600 focus:ring-cyan-600" @checked(old('jewelry_removed') === 'Tidak terlepas' || old('jewelry_removed') === null)>
                                        <span>Tidak terlepas</span>
                                    </label>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 p-4">
                                <p class="font-semibold text-slate-900">Operasi sebelumnya</p>
                                <div class="mt-3 flex flex-wrap items-center gap-2 text-sm font-medium text-slate-700">
                                    <input type="checkbox" name="has_previous_surgery" value="Ada riwayat operasi" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-600" @checked(old('has_previous_surgery') === 'Ada riwayat operasi')>
                                    <span>Ada riwayat operasi</span>
                                </div>
                            </div>

                            <label class="space-y-2 rounded-2xl border border-slate-200 p-4">
                                <span class="text-sm font-semibold text-slate-700">Kapan operasi sebelumnya dilakukan</span>
                                <input type="date" name="previous_surgery_date" value="{{ old('previous_surgery_date') }}" class="w-full rounded-[10px] border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                                @error('previous_surgery_date')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                            </label>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="space-y-2 rounded-2xl border border-slate-200 p-4">
                                <span class="text-sm font-semibold text-slate-700">Riwayat penyakit</span>
                                <textarea name="disease_history" rows="4" class="w-full rounded-[10px] border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600" placeholder="Riwayat penyakit yang relevan">{{ old('disease_history') }}</textarea>
                                @error('disease_history')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                            </label>

                            <label class="space-y-2 rounded-2xl border border-slate-200 p-4">
                                <span class="text-sm font-semibold text-slate-700">Obat yang dikonsumsi</span>
                                <textarea name="current_medications" rows="4" class="w-full rounded-[10px] border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600" placeholder="Daftar obat yang sedang dikonsumsi">{{ old('current_medications') }}</textarea>
                                @error('current_medications')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                            </label>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="space-y-2 rounded-2xl border border-slate-200 p-4">
                                <span class="text-sm font-semibold text-slate-700">Operasi sebelumnya / catatan</span>
                                <textarea name="previous_surgery_note" rows="4" class="w-full rounded-[10px] border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600" placeholder="Deskripsikan jika pernah operasi sebelumnya">{{ old('previous_surgery_note') }}</textarea>
                                @error('previous_surgery_note')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                            </label>

                            <label class="space-y-2 rounded-2xl border border-slate-200 p-4">
                                <span class="text-sm font-semibold text-slate-700">Catatan terakhir</span>
                                <textarea name="final_note" rows="4" class="w-full rounded-[10px] border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600" placeholder="Catatan penutup dari perawat">{{ old('final_note') }}</textarea>
                                @error('final_note')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                            </label>
                        </div>
                    </div>
                </section>

                <div class="flex flex-wrap items-center justify-end gap-3 pb-2">
                    <a href="{{ route('dashboard.nurse.regular') }}" class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Kembali 
                    </a>
                    <button type="submit" class="rounded-2xl bg-cyan-700 px-5 py-3 text-sm font-semibold text-white hover:bg-cyan-800">
                        Simpan pengajuan operasi
                    </button>
                </div>
            </form>
        </div>

        <aside class="space-y-6 xl:sticky xl:top-6 xl:self-start">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Ringkasan Flow</h2>
                <ol class="mt-4 space-y-3 text-sm text-slate-600">
                    <li class="flex gap-3"><span class="mt-0.5 h-6 w-6 rounded-full bg-cyan-100 text-center text-xs font-bold leading-6 text-cyan-700">1</span>Isi No RM dan biodata pasien.</li>
                    <li class="flex gap-3"><span class="mt-0.5 h-6 w-6 rounded-full bg-cyan-100 text-center text-xs font-bold leading-6 text-cyan-700">2</span>Pilih diagnosis ICD-10 dan tindakan ICD-9 CM.</li>
                    <li class="flex gap-3"><span class="mt-0.5 h-6 w-6 rounded-full bg-cyan-100 text-center text-xs font-bold leading-6 text-cyan-700">3</span>Lengkapi checklist, unggah dokumen, dan beri prioritas pasien.</li>
                    <li class="flex gap-3"><span class="mt-0.5 h-6 w-6 rounded-full bg-cyan-100 text-center text-xs font-bold leading-6 text-cyan-700">4</span>Simpan pengajuan untuk diteruskan ke alur operasi.</li>
                </ol>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Prioritas warna</p>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center gap-3 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2">
                        <span class="h-3 w-3 shrink-0 rounded-full bg-rose-500"></span>
                        <div><span class="font-semibold text-rose-700">Imminent</span><span class="text-rose-600"> — Ancaman jiwa, perlu tindakan segera (menit)</span></div>
                    </div>
                    <div class="flex items-center gap-3 rounded-xl border border-orange-200 bg-orange-50 px-3 py-2">
                        <span class="h-3 w-3 shrink-0 rounded-full bg-orange-500"></span>
                        <div><span class="font-semibold text-orange-700">Cito</span><span class="text-orange-600"> — Gawat darurat, perlu tindakan dalam hitungan jam</span></div>
                    </div>
                    <div class="flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2">
                        <span class="h-3 w-3 shrink-0 rounded-full bg-amber-500"></span>
                        <div><span class="font-semibold text-amber-700">Urgent</span><span class="text-amber-600"> — Mendesak, perlu ditangani dalam 24-48 jam</span></div>
                    </div>
                    <div class="flex items-center gap-3 rounded-xl border border-sky-200 bg-sky-50 px-3 py-2">
                        <span class="h-3 w-3 shrink-0 rounded-full bg-sky-500"></span>
                        <div><span class="font-semibold text-sky-700">Expedited</span><span class="text-sky-600"> — Dipercepat, penanganan dalam beberapa hari</span></div>
                    </div>
                    <div class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2">
                        <span class="h-3 w-3 shrink-0 rounded-full bg-emerald-500"></span>
                        <div><span class="font-semibold text-emerald-700">Elektif</span><span class="text-emerald-600"> — Terjadwal, dapat direncanakan sebelumnya</span></div>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function showUploadNotification(msg) {
                var el = document.getElementById('upload-notification');
                if (!el) return;
                el.textContent = msg;
                el.classList.remove('hidden');
                setTimeout(function () { el.classList.add('hidden'); }, 3500);
            }

            document.querySelectorAll('[data-file-wrapper]').forEach(function (wrapper) {
                wrapper.addEventListener('click', function () {
                    var input = this.querySelector('input[type="file"]');
                    if (input && input.disabled) {
                        showUploadNotification('Centang checkbox "Tersedia" terlebih dahulu sebelum mengunggah file.');
                    }
                });
            });

            const birthDateInput = document.getElementById('birth_date');
            const ageDisplay = document.getElementById('age_display');

            const updateAge = () => {
                if (!birthDateInput || !ageDisplay || !birthDateInput.value) {
                    ageDisplay.value = '';
                    return;
                }

                const birthDate = new Date(birthDateInput.value);
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const monthDifference = today.getMonth() - birthDate.getMonth();

                if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < birthDate.getDate())) {
                    age -= 1;
                }

                ageDisplay.value = Number.isFinite(age) && age >= 0 ? `${age} tahun` : '';
            };

            birthDateInput?.addEventListener('change', updateAge);
            birthDateInput?.addEventListener('input', updateAge);
            updateAge();

            // Toggle file upload fields when checkbox is checked
            document.querySelectorAll('[data-toggle-target]').forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    var targetId = this.dataset.toggleTarget;
                    var fileInput = document.querySelector('[data-toggle-id="' + targetId + '"]');
                    if (fileInput) {
                        fileInput.disabled = !this.checked;
                    }
                });
            });

            // Vital signs: radio-like checkbox behavior + toggle textfield
            var vitalOptions = document.querySelectorAll('[data-vital-option]');
            var vitalInput = document.getElementById('vital_sign_stable_value');
            var vitalWrapper = document.getElementById('vital_sign_note_wrapper');

            vitalOptions.forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    vitalOptions.forEach(function (cb) {
                        if (cb !== checkbox) cb.checked = false;
                    });
                    vitalInput.value = this.dataset.vitalOption;
                    if (this.dataset.vitalOption === 'Stabil') {
                        vitalWrapper.classList.remove('hidden');
                    } else {
                        vitalWrapper.classList.add('hidden');
                    }
                });
            });
        });
    </script>
</x-app-layout>