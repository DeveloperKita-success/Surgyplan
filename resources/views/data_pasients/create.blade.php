<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Master data</p>
            <h1 class="text-2xl font-bold text-slate-900">Tambah Patient</h1>
        </div>
    </x-slot>

    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <form method="POST" action="{{ route('patients.store') }}" class="space-y-6">
            @csrf

            @if ($errors->any())
                <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    <p class="font-semibold">Periksa kembali input patient.</p>
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
                    <input name="medical_record_number" value="{{ old('medical_record_number') }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600" required>
                </label>

                <label class="space-y-2">
                    <span class="text-sm font-medium text-slate-600">Nama Patient</span>
                    <input name="name" value="{{ old('name') }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600" required>
                </label>

                <label class="space-y-2">
                    <span class="text-sm font-medium text-slate-600">Tanggal Lahir</span>
                    <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                </label>

                <label class="space-y-2">
                    <span class="text-sm font-medium text-slate-600">Umur</span>
                    <input type="number" name="age" value="{{ old('age') }}" min="0" max="130" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                </label>

                <label class="space-y-2">
                    <span class="text-sm font-medium text-slate-600">Jenis Kelamin</span>
                    <select name="gender" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600" required>
                        <option value="">Pilih jenis kelamin</option>
                        @foreach (['Laki-laki', 'Perempuan'] as $option)
                            <option value="{{ $option }}" @selected(old('gender') === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="space-y-2">
                    <span class="text-sm font-medium text-slate-600">Ruang Asal</span>
                    <select name="origin_room" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600" required>
                        <option value="">Pilih ruang asal</option>
                        @foreach (['IGD', 'Bangsal', 'Poli'] as $room)
                            <option value="{{ $room }}" @selected(old('origin_room') === $room)>{{ $room }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="space-y-2">
                    <span class="text-sm font-medium text-slate-600">Telepon</span>
                    <input name="phone" value="{{ old('phone') }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                </label>

                <label class="space-y-2 md:col-span-2">
                    <span class="text-sm font-medium text-slate-600">Alamat</span>
                    <textarea name="address" rows="3" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">{{ old('address') }}</textarea>
                </label>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('patients.index') }}" class="rounded-lg border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
                <button type="submit" class="rounded-lg bg-cyan-700 px-5 py-3 text-sm font-semibold text-white hover:bg-cyan-800">Simpan Patient</button>
            </div>
        </form>
    </section>
</x-app-layout>
