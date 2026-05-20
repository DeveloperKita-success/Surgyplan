<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Pengajuan operasi</p>
            <h1 class="text-2xl font-bold text-slate-900">Buat Pengajuan Baru</h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('nurse-regular.surgery-requests.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">1. Data Pasien</h2>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Nomor Rekam Medis</span>
                        <input name="medical_record_number" value="{{ old('medical_record_number') }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                        @error('medical_record_number') <span class="text-sm text-rose-600">{{ $message }}</span> @enderror
                    </label>
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Nama Pasien</span>
                        <input name="patient_name" value="{{ old('patient_name') }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                        @error('patient_name') <span class="text-sm text-rose-600">{{ $message }}</span> @enderror
                    </label>
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Tanggal Lahir</span>
                        <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                    </label>
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Usia</span>
                        <input type="number" name="age" value="{{ old('age') }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                    </label>
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Jenis Kelamin</span>
                        <select name="gender" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                            <option value="">Pilih jenis kelamin</option>
                            @foreach (['Laki-laki', 'Perempuan'] as $gender)
                                <option value="{{ $gender }}" @selected(old('gender') === $gender)>{{ $gender }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Asal Ruangan</span>
                        <select name="origin_room" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                            <option value="">Pilih ruangan</option>
                            @foreach (['IGD', 'Bangsal', 'Poli'] as $room)
                                <option value="{{ $room }}" @selected(old('origin_room') === $room)>{{ $room }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="space-y-2 md:col-span-2">
                        <span class="text-sm font-medium text-slate-600">Alamat</span>
                        <textarea name="address" rows="2" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">{{ old('address') }}</textarea>
                    </label>
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Telepon</span>
                        <input name="phone" value="{{ old('phone') }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                    </label>
                </div>
            </section>

            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">2. Diagnosis dan Tindakan Operasi</h2>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Diagnosis ICD-10</span>
                        <input name="diagnosis_text" value="{{ old('diagnosis_text') }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600" placeholder="Contoh: K35.8 - Appendicitis akut">
                        @error('diagnosis_text') <span class="text-sm text-rose-600">{{ $message }}</span> @enderror
                    </label>
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Tindakan ICD-9 CM</span>
                        <input name="procedure_text" value="{{ old('procedure_text') }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600" placeholder="Contoh: 47.01 - Appendektomi">
                        @error('procedure_text') <span class="text-sm text-rose-600">{{ $message }}</span> @enderror
                    </label>
                </div>
            </section>

            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">3. Checklist Kondisi Pasien</h2>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    @foreach ([
                        ['name' => 'surgical_consent', 'label' => 'Informed consent bedah', 'yes' => 'Ada', 'no' => 'Belum ada', 'file' => 'surgical_consent_file'],
                        ['name' => 'anesthesia_consent', 'label' => 'Informed consent anestesi', 'yes' => 'Ada', 'no' => 'Belum ada', 'file' => 'anesthesia_consent_file'],
                        ['name' => 'lab_result_complete', 'label' => 'Hasil lab lengkap', 'yes' => 'Lengkap', 'no' => 'Belum lengkap', 'file' => 'lab_result_file'],
                        ['name' => 'radiology_available', 'label' => 'Hasil radiologi', 'yes' => 'Tersedia', 'no' => 'Tidak tersedia', 'file' => 'radiology_file'],
                        ['name' => 'anesthesia_consultation_done', 'label' => 'Konsultasi anestesi', 'yes' => 'Sudah dikonsultasikan', 'no' => 'Belum konsultasi', 'file' => null],
                        ['name' => 'vital_sign_stable', 'label' => 'Tanda vital', 'yes' => 'Stabil', 'no' => 'Beresiko', 'file' => null],
                        ['name' => 'fasting_more_than_6_hours', 'label' => 'Status puasa', 'yes' => 'Lebih dari 6 jam', 'no' => 'Kurang dari 6 jam', 'file' => null],
                        ['name' => 'blood_available', 'label' => 'Darah tersedia', 'yes' => 'Tersedia', 'no' => 'Tidak tersedia', 'file' => null],
                        ['name' => 'infusion_installed', 'label' => 'Infus', 'yes' => 'Terpasang', 'no' => 'Tidak terpasang', 'file' => null],
                        ['name' => 'catheter_installed', 'label' => 'Kateter', 'yes' => 'Terpasang', 'no' => 'Tidak terpasang', 'file' => null],
                        ['name' => 'surgical_area_shaved', 'label' => 'Pencukuran area operasi', 'yes' => 'Sudah dilakukan', 'no' => 'Belum dilakukan', 'file' => null],
                        ['name' => 'jewelry_removed', 'label' => 'Pelepasan perhiasan', 'yes' => 'Sudah terlepas', 'no' => 'Belum', 'file' => null],
                        ['name' => 'has_previous_surgery', 'label' => 'Operasi sebelumnya', 'yes' => 'Pernah', 'no' => 'Belum pernah', 'file' => null],
                    ] as $field)
                        <div class="rounded-lg border border-slate-200 p-4">
                            <p class="text-sm font-medium text-slate-700">{{ $field['label'] }}</p>
                            <div class="mt-3 flex flex-wrap gap-4 text-sm text-slate-600">
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="{{ $field['name'] }}" value="1" @checked(old($field['name']) === '1') class="border-slate-300 text-cyan-700 focus:ring-cyan-600">
                                    <span>{{ $field['yes'] }}</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="{{ $field['name'] }}" value="0" @checked(old($field['name']) === '0') class="border-slate-300 text-cyan-700 focus:ring-cyan-600">
                                    <span>{{ $field['no'] }}</span>
                                </label>
                            </div>
                            @if ($field['file'])
                                <input type="file" name="{{ $field['file'] }}" class="mt-3 block w-full text-sm text-slate-600">
                            @endif
                        </div>
                    @endforeach
                    <label class="space-y-2 md:col-span-2">
                        <span class="text-sm font-medium text-slate-600">Estimasi Risiko Anestesi</span>
                        <textarea name="anesthesia_risk_estimation" rows="2" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">{{ old('anesthesia_risk_estimation') }}</textarea>
                    </label>
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Tekanan Darah</span>
                        <input name="blood_pressure" value="{{ old('blood_pressure') }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                    </label>
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Golongan Darah</span>
                        <input name="blood_type" value="{{ old('blood_type') }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                    </label>
                    <label class="space-y-2 md:col-span-2">
                        <span class="text-sm font-medium text-slate-600">Catatan Tanda Vital</span>
                        <textarea name="vital_sign_note" rows="2" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">{{ old('vital_sign_note') }}</textarea>
                    </label>
                    <label class="space-y-2 md:col-span-2">
                        <span class="text-sm font-medium text-slate-600">Alergi</span>
                        <textarea name="allergy" rows="2" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">{{ old('allergy') }}</textarea>
                    </label>
                    <label class="space-y-2 md:col-span-2">
                        <span class="text-sm font-medium text-slate-600">Riwayat Penyakit</span>
                        <textarea name="disease_history" rows="2" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">{{ old('disease_history') }}</textarea>
                    </label>
                    <label class="space-y-2 md:col-span-2">
                        <span class="text-sm font-medium text-slate-600">Obat Saat Ini</span>
                        <textarea name="current_medications" rows="2" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">{{ old('current_medications') }}</textarea>
                    </label>
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Tanggal Operasi Sebelumnya</span>
                        <input type="date" name="previous_surgery_date" value="{{ old('previous_surgery_date') }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                    </label>
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Catatan Operasi Sebelumnya</span>
                        <input name="previous_surgery_note" value="{{ old('previous_surgery_note') }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                    </label>
                    <label class="space-y-2 md:col-span-2">
                        <span class="text-sm font-medium text-slate-600">Catatan Akhir</span>
                        <textarea name="final_note" rows="2" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">{{ old('final_note') }}</textarea>
                    </label>
                </div>
            </section>

            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">4. Prioritas dan Jadwal Usulan</h2>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Prioritas Pasien</span>
                        <select name="patient_priority" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                            <option value="">Pilih prioritas</option>
                            @foreach (['imminent', 'cito', 'urgent', 'expedited', 'elektif'] as $priority)
                                <option value="{{ $priority }}" @selected(old('patient_priority') === $priority)>{{ ucfirst($priority) }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Dokter Operasi</span>
                        <select name="requested_doctor_id" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                            <option value="">Pilih dokter</option>
                            @foreach ($doctors as $doctor)
                                <option value="{{ $doctor->id }}" @selected((string) old('requested_doctor_id') === (string) $doctor->id)>
                                    {{ trim(($doctor->title ? $doctor->title.' ' : '').$doctor->user->name) }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Tanggal Operasi</span>
                        <input type="date" name="requested_date" value="{{ old('requested_date') }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                    </label>
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Jam Operasi</span>
                        <input type="time" name="requested_start_time" value="{{ old('requested_start_time') }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                    </label>
                    <label class="space-y-2 md:col-span-2">
                        <span class="text-sm font-medium text-slate-600">Catatan Pengajuan</span>
                        <textarea name="notes" rows="3" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">{{ old('notes') }}</textarea>
                    </label>
                </div>
            </section>

            <div class="flex justify-end">
                <button type="submit" class="rounded-lg bg-cyan-700 px-5 py-3 text-sm font-semibold text-white hover:bg-cyan-800">
                    Ajukan Operasi
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
