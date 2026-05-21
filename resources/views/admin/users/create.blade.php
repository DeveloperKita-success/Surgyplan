<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Kontrol akses dan akun</p>
            <h1 class="text-2xl font-bold text-slate-900">Tambah User</h1>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl" x-data="{ role: '{{ old('role') }}', nurseIsUk: '{{ old('nurse_is_uk', '1') }}' }">
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
            @csrf

            <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="text-sm font-semibold text-slate-700" for="name">Nama</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name') }}"
                            class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                            required
                        />
                        @error('name')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="text-sm font-semibold text-slate-700" for="email">Email</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                            required
                        />
                        @error('email')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-700" for="role">Role</label>
                        <select
                            id="role"
                            name="role"
                            x-model="role"
                            class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                            required
                        >
                            <option value="" disabled {{ old('role') ? '' : 'selected' }}>Pilih role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>
                                    {{ $role === 'perawat' ? 'Perawat' : 'Dokter' }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-700" for="password">Password</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                            required
                        />
                        @error('password')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div
                class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm"
                x-show="role === 'dokter'"
                x-transition
            >
                <h2 class="text-sm font-semibold text-slate-700">Data Dokter</h2>
                <div class="mt-4 grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="text-sm font-semibold text-slate-700" for="specialist_id">Spesialis</label>
                        <select
                            id="specialist_id"
                            name="specialist_id"
                            x-bind:required="role === 'dokter'"
                            class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                        >
                            <option value="" disabled {{ old('specialist_id') ? '' : 'selected' }}>Pilih spesialis</option>
                            @foreach ($specialists as $specialist)
                                <option value="{{ $specialist->id }}" {{ (string) old('specialist_id') === (string) $specialist->id ? 'selected' : '' }}>
                                    {{ $specialist->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('specialist_id')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-700" for="title">Title</label>
                        <input
                            id="title"
                            name="title"
                            type="text"
                            value="{{ old('title') }}"
                            x-bind:required="role === 'dokter'"
                            class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                        />
                        @error('title')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-700" for="str_number">STR Number</label>
                        <input
                            id="str_number"
                            name="str_number"
                            type="text"
                            value="{{ old('str_number') }}"
                            x-bind:required="role === 'dokter'"
                            class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                        />
                        @error('str_number')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-700" for="sip_number">SIP Number</label>
                        <input
                            id="sip_number"
                            name="sip_number"
                            type="text"
                            value="{{ old('sip_number') }}"
                            x-bind:required="role === 'dokter'"
                            class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                        />
                        @error('sip_number')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div
                class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm"
                x-show="role === 'perawat'"
                x-transition
            >
                <h2 class="text-sm font-semibold text-slate-700">Data Perawat</h2>
                <div class="mt-4 space-y-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-700">Apakah Perawat OK?</p>
                        <div class="mt-2 flex flex-wrap gap-4">
                            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                <input
                                    type="radio"
                                    name="nurse_is_uk"
                                    value="1"
                                    x-model="nurseIsUk"
                                    class="h-4 w-4 border-slate-300 text-emerald-600 focus:ring-emerald-200"
                                />
                                Ya (Perawat OK)
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                <input
                                    type="radio"
                                    name="nurse_is_uk"
                                    value="0"
                                    x-model="nurseIsUk"
                                    class="h-4 w-4 border-slate-300 text-emerald-600 focus:ring-emerald-200"
                                />
                                Tidak (Perawat Reguler)
                            </label>
                        </div>
                        @error('nurse_is_uk')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-show="nurseIsUk === '0'" x-transition>
                        <label class="text-sm font-semibold text-slate-700" for="origin_unit">Unit Asal</label>
                        <select
                            id="origin_unit"
                            name="origin_unit"
                            x-bind:required="role === 'perawat' && nurseIsUk === '0'"
                            class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                        >
                            <option value="" disabled {{ old('origin_unit') ? '' : 'selected' }}>Pilih unit asal</option>
                            @foreach (['Bangsal', 'IGD', 'Poli'] as $unit)
                                <option value="{{ $unit }}" {{ old('origin_unit') === $unit ? 'selected' : '' }}>
                                    {{ $unit }}
                                </option>
                            @endforeach
                        </select>
                        @error('origin_unit')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-3">
                <a
                    href="{{ route('admin.users.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                >
                    Batal
                </a>
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700"
                >
                    Simpan User
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
