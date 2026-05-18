<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Master data</p>
            <h1 class="text-2xl font-bold text-slate-900">Edit Pasien</h1>
        </div>
    </x-slot>

    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <form method="POST" action="{{ route('patients.update', $patient) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    <p class="font-semibold">Periksa kembali input Anda:</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid gap-4 md:grid-cols-2">
                <label class="space-y-2">
                    <span class="text-sm font-medium text-slate-600">Nomor Rekam Medis</span>
                    <input
                        type="text"
                        name="medical_record_number"
                        value="{{ old('medical_record_number', $patient->medical_record_number) }}"
                        class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600"
                        required
                    />
                </label>

                <label class="space-y-2">
                    <span class="text-sm font-medium text-slate-600">Nama Pasien</span>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $patient->name) }}"
                        class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600"
                        required
                    />
                </label>

                <label class="space-y-2">
                    <span class="text-sm font-medium text-slate-600">Tanggal Lahir (opsional)</span>
                    <input
                        type="date"
                        name="birth_date"
                        value="{{ old('birth_date', optional($patient->birth_date)->format('Y-m-d')) }}"
                        class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600"
                    />
                    <p class="text-xs text-slate-500">Jika diisi, umur akan dihitung otomatis.</p>
                </label>

                <label class="space-y-2">
                    <span class="text-sm font-medium text-slate-600">Umur (opsional)</span>
                    <input
                        type="number"
                        name="age"
                        value="{{ old('age', $patient->age) }}"
                        min="0"
                        max="130"
                        class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600"
                    />
                </label>

                <label class="space-y-2">
                    <span class="text-sm font-medium text-slate-600">Gender</span>
                    <select name="gender" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600" required>
                        <option value="" @selected(old('gender', $patient->gender) === null || old('gender', $patient->gender) === '')>Pilih</option>
                        <option value="L" @selected(old('gender', $patient->gender) === 'L')>Laki-laki</option>
                        <option value="P" @selected(old('gender', $patient->gender) === 'P')>Perempuan</option>
                    </select>
                </label>

                <label class="space-y-2">
                    <span class="text-sm font-medium text-slate-600">Ruang Asal (opsional)</span>
                    <select name="origin_room" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                        <option value="" @selected(old('origin_room', $patient->origin_room) === null || old('origin_room', $patient->origin_room) === '')>-</option>
                        <option value="IGD" @selected(old('origin_room', $patient->origin_room) === 'IGD')>IGD</option>
                        <option value="Bangsal" @selected(old('origin_room', $patient->origin_room) === 'Bangsal')>Bangsal</option>
                        <option value="Poli" @selected(old('origin_room', $patient->origin_room) === 'Poli')>Poli</option>
                    </select>
                </label>

                <label class="space-y-2 md:col-span-2">
                    <span class="text-sm font-medium text-slate-600">Alamat (opsional)</span>
                    <textarea
                        name="address"
                        rows="3"
                        class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600"
                    >{{ old('address', $patient->address) }}</textarea>
                </label>

                <label class="space-y-2">
                    <span class="text-sm font-medium text-slate-600">No. HP (opsional)</span>
                    <input
                        type="text"
                        name="phone"
                        value="{{ old('phone', $patient->phone) }}"
                        class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600"
                    />
                </label>
            </div>

            <div class="flex items-center justify-end gap-2">
                <a href="{{ route('patients.show', $patient) }}" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
                <button type="submit" class="rounded-lg bg-cyan-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-cyan-800">Simpan Perubahan</button>
            </div>
        </form>
    </section>
</x-app-layout>
