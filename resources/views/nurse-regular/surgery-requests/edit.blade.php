<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Edit pengajuan</p>
            <h1 class="text-2xl font-bold text-slate-900">{{ $surgeryRequest->patient->name }}</h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        <form method="POST" action="{{ route('nurse-regular.surgery-requests.update', $surgeryRequest) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Data Utama</h2>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">No RM</span>
                        <input name="medical_record_number" value="{{ old('medical_record_number', $surgeryRequest->patient->medical_record_number) }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                    </label>
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Nama Pasien</span>
                        <input name="patient_name" value="{{ old('patient_name', $surgeryRequest->patient->name) }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                    </label>
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Umur</span>
                        <input name="age" value="{{ old('age', $surgeryRequest->patient->age) }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                    </label>
                    <label class="space-y-2" x-data="icdAutocomplete('icd10')">
                        <span class="text-sm font-medium text-slate-600">Diagnosis ICD-10</span>
                        <div class="relative">
                            <input type="text" name="diagnosis_text" x-model="query"
                                x-on:input.debounce.300ms="search"
                                x-on:blur="handleBlur"
                                x-on:keydown.down.prevent="nextItem"
                                x-on:keydown.up.prevent="prevItem"
                                x-on:keydown.enter.prevent="selectItem"
                                x-on:keydown.escape="open = false"
                                value="{{ old('diagnosis_text', $surgeryRequest->diagnosis_text) }}"
                                class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600"
                                placeholder="Ketik diagnosis atau kode ICD-10..."
                                autocomplete="off">
                            <div x-show="open && results.length > 0"
                                x-transition x-cloak
                                class="absolute z-50 mt-1 w-full rounded-lg border border-slate-200 bg-white shadow-lg">
                                <template x-for="(item, idx) in results" :key="idx">
                                    <button type="button"
                                        x-on:click="selectItem(idx)"
                                        x-on:mousemove="hovered = idx"
                                        class="w-full px-4 py-2 text-left text-sm transition"
                                        :class="idx === hovered ? 'bg-cyan-50 text-cyan-800' : 'text-slate-700 hover:bg-slate-50'">
                                        <span class="font-semibold" x-text="item.code"></span>
                                        <span class="ml-2" x-text="item.name"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        @error('diagnosis_text') <span class="text-sm text-rose-600">{{ $message }}</span> @enderror
                    </label>
                    <label class="space-y-2" x-data="icdAutocomplete('icd9')">
                        <span class="text-sm font-medium text-slate-600">Tindakan ICD-9 CM</span>
                        <div class="relative">
                            <input type="text" name="procedure_text" x-model="query"
                                x-on:input.debounce.300ms="search"
                                x-on:blur="handleBlur"
                                x-on:keydown.down.prevent="nextItem"
                                x-on:keydown.up.prevent="prevItem"
                                x-on:keydown.enter.prevent="selectItem"
                                x-on:keydown.escape="open = false"
                                value="{{ old('procedure_text', $surgeryRequest->procedure_text) }}"
                                class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600"
                                placeholder="Ketik tindakan atau kode ICD-9-CM..."
                                autocomplete="off">
                            <div x-show="open && results.length > 0"
                                x-transition x-cloak
                                class="absolute z-50 mt-1 w-full rounded-lg border border-slate-200 bg-white shadow-lg">
                                <template x-for="(item, idx) in results" :key="idx">
                                    <button type="button"
                                        x-on:click="selectItem(idx)"
                                        x-on:mousemove="hovered = idx"
                                        class="w-full px-4 py-2 text-left text-sm transition"
                                        :class="idx === hovered ? 'bg-cyan-50 text-cyan-800' : 'text-slate-700 hover:bg-slate-50'">
                                        <span class="font-semibold" x-text="item.code"></span>
                                        <span class="ml-2" x-text="item.name"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        @error('procedure_text') <span class="text-sm text-rose-600">{{ $message }}</span> @enderror
                    </label>
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Tanggal Lahir</span>
                        <input type="date" name="birth_date" value="{{ old('birth_date', $surgeryRequest->patient->birth_date?->format('Y-m-d')) }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                    </label>
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Jenis Kelamin</span>
                        <select name="gender" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                            @foreach (['Laki-laki', 'Perempuan'] as $gender)
                                <option value="{{ $gender }}" @selected(old('gender', $surgeryRequest->patient->gender) === $gender)>{{ $gender }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Ruang Asal</span>
                        <select name="origin_room" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                            @foreach (['IGD', 'Bangsal', 'Poli'] as $room)
                                <option value="{{ $room }}" @selected(old('origin_room', $surgeryRequest->patient->origin_room) === $room)>{{ $room }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="space-y-2 md:col-span-2">
                        <span class="text-sm font-medium text-slate-600">Alamat</span>
                        <textarea name="address" rows="2" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">{{ old('address', $surgeryRequest->patient->address) }}</textarea>
                    </label>
                    <label class="space-y-2 md:col-span-2">
                        <span class="text-sm font-medium text-slate-600">Telepon</span>
                        <input name="phone" value="{{ old('phone', $surgeryRequest->patient->phone) }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                    </label>
                </div>
            </section>

            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Jadwal dan Status</h2>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Status Pasien</span>
                        <select name="patient_priority" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                            @foreach (['imminent', 'cito', 'urgent', 'expedited', 'elektif'] as $priority)
                                <option value="{{ $priority }}" @selected(old('patient_priority', $surgeryRequest->patient_priority) === $priority)>{{ ucfirst($priority) }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Dokter</span>
                        <select name="requested_doctor_id" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                            @foreach ($doctors as $doctor)
                                <option value="{{ $doctor->id }}" @selected((string) old('requested_doctor_id', $surgeryRequest->requested_doctor_id) === (string) $doctor->id)>
                                    {{ trim(($doctor->title ? $doctor->title.' ' : '').$doctor->user->name) }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Tanggal Operasi</span>
                        <input type="date" name="requested_date" value="{{ old('requested_date', $surgeryRequest->requested_date?->format('Y-m-d')) }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                    </label>
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Jam Operasi</span>
                        <input type="time" name="requested_start_time" value="{{ old('requested_start_time', $surgeryRequest->requested_start_time) }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                    </label>
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Estimasi Resiko</span>
                        <input name="risk_estimation" value="{{ old('risk_estimation', $surgeryRequest->risk_estimation) }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600" placeholder="Contoh: ASA II, risiko sedang">
                    </label>
                    <label class="space-y-2 md:col-span-2">
                        <span class="text-sm font-medium text-slate-600">Catatan Pengajuan</span>
                        <textarea name="notes" rows="3" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">{{ old('notes', $surgeryRequest->notes) }}</textarea>
                    </label>
                </div>
            </section>

            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Checklist Kondisi Pasien</h2>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    @foreach ([
                        ['surgical_consent', 'Informed consent bedah'],
                        ['anesthesia_consent', 'Informed consent anestesi'],
                        ['lab_result_complete', 'Hasil lab lengkap'],
                        ['radiology_available', 'Hasil radiologi tersedia'],
                        ['anesthesia_consultation_done', 'Konsultasi anestesi selesai'],
                        ['vital_sign_stable', 'Tanda vital stabil'],
                        ['fasting_more_than_6_hours', 'Puasa lebih dari 6 jam'],
                        ['blood_available', 'Darah tersedia'],
                        ['infusion_installed', 'Infus terpasang'],
                        ['catheter_installed', 'Kateter terpasang'],
                        ['surgical_area_shaved', 'Area operasi dicukur'],
                        ['jewelry_removed', 'Perhiasan dilepas'],
                        ['has_previous_surgery', 'Pernah operasi sebelumnya'],
                    ] as [$name, $label])
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-slate-600">{{ $label }}</span>
                            <select name="{{ $name }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                                <option value="1" @selected((string) old($name, (int) $surgeryRequest->preoperativeChecklist->{$name}) === '1')>Ya</option>
                                <option value="0" @selected((string) old($name, (int) $surgeryRequest->preoperativeChecklist->{$name}) === '0')>Tidak</option>
                            </select>
                        </label>
                    @endforeach
                    @foreach ([
                        ['anesthesia_risk_estimation', 'Estimasi Risiko Anestesi'],
                        ['vital_sign_note', 'Catatan Tanda Vital'],
                        ['blood_pressure', 'Tekanan Darah'],
                        ['allergy', 'Alergi'],
                        ['blood_type', 'Golongan Darah'],
                        ['disease_history', 'Riwayat Penyakit'],
                        ['current_medications', 'Obat yang Dikonsumsi'],
                        ['previous_surgery_note', 'Catatan Operasi Sebelumnya'],
                        ['final_note', 'Catatan Terakhir'],
                    ] as [$name, $label])
                        <label class="space-y-2 md:col-span-2">
                            <span class="text-sm font-medium text-slate-600">{{ $label }}</span>
                            <textarea name="{{ $name }}" rows="2" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">{{ old($name, $surgeryRequest->preoperativeChecklist->{$name}) }}</textarea>
                        </label>
                    @endforeach
                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Tanggal Operasi Sebelumnya</span>
                        <input type="date" name="previous_surgery_date" value="{{ old('previous_surgery_date', $surgeryRequest->preoperativeChecklist->previous_surgery_date?->format('Y-m-d')) }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                    </label>
                </div>
            </section>

            <div class="flex justify-end gap-3">
                <a href="{{ route('nurse-regular.surgery-requests.show', $surgeryRequest) }}" class="rounded-lg border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Batal
                </a>
                <button type="submit" class="rounded-lg bg-cyan-700 px-5 py-3 text-sm font-semibold text-white hover:bg-cyan-800">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
