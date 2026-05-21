<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="auth-title text-2xl">Buat Akun</h1>
        <p class="auth-subtitle text-sm">Pilih peran Anda untuk melengkapi data yang diperlukan.</p>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="form-control block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="form-control block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="form-control block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="form-control block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-input-label for="role" :value="__('Daftar Sebagai')" />
            <select
                id="role"
                name="role"
                class="form-control mt-1 block w-full rounded-md border-slate-300 bg-white text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                required
            >
                <option value="" disabled {{ old('role') ? '' : 'selected' }}>Pilih jenis akun</option>
                <option value="dokter" {{ old('role') === 'dokter' ? 'selected' : '' }}>Dokter</option>
                <option value="perawat" {{ old('role') === 'perawat' ? 'selected' : '' }}>Perawat</option>
            </select>
            <p class="mt-2 text-xs text-slate-500">Jika memilih Perawat, pilih juga apakah Perawat OK atau Bukan.</p>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Doctor fields -->
        <div id="doctorFields" class="mt-4 space-y-4" aria-hidden="true">
            <div>
                <x-input-label for="specialist_id" :value="__('Spesialis')" />
                <select
                    id="specialist_id"
                    name="specialist_id"
                    class="form-control mt-1 block w-full rounded-md border-slate-300 bg-white text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                >
                    <option value="" disabled {{ old('specialist_id') ? '' : 'selected' }}>Pilih spesialis</option>
                    @foreach (($specialists ?? collect()) as $specialist)
                        <option value="{{ $specialist->id }}" {{ (string) old('specialist_id') === (string) $specialist->id ? 'selected' : '' }}>
                            {{ $specialist->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('specialist_id')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="title" :value="__('Gelar / Jabatan')" />
                <x-text-input id="title" class="form-control mt-1 block w-full" type="text" name="title" :value="old('title')" autocomplete="off" />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="str_number" :value="__('Nomor STR')" />
                <x-text-input id="str_number" class="form-control mt-1 block w-full" type="text" name="str_number" :value="old('str_number')" autocomplete="off" />
                <x-input-error :messages="$errors->get('str_number')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="sip_number" :value="__('Nomor SIP / Izin Praktik')" />
                <x-text-input id="sip_number" class="form-control mt-1 block w-full" type="text" name="sip_number" :value="old('sip_number')" autocomplete="off" />
                <x-input-error :messages="$errors->get('sip_number')" class="mt-2" />
            </div>
        </div>

        <!-- Nurse fields -->
        <div id="nurseFields" class="mt-4 space-y-4" aria-hidden="true">
            <div>
                <x-input-label for="nurse_type" :value="__('Sebagai Perawat OK?')" />
                <select
                    id="nurse_type"
                    name="nurse_type"
                    class="form-control mt-1 block w-full rounded-md border-slate-300 bg-white text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                >
                    <option value="" disabled {{ old('nurse_type') ? '' : 'selected' }}>Pilih opsi</option>
                    <option value="perawat_ok" {{ old('nurse_type') === 'perawat_ok' ? 'selected' : '' }}>Ya, Perawat OK</option>
                    <option value="perawat_biasa" {{ old('nurse_type') === 'perawat_biasa' ? 'selected' : '' }}>Bukan Perawat OK</option>
                </select>
                <x-input-error :messages="$errors->get('nurse_type')" class="mt-2" />
            </div>

            <div id="unitFields" aria-hidden="true">
                <x-input-label for="origin_unit" :value="__('Asal Unit')" />
                <select
                    id="origin_unit"
                    name="origin_unit"
                    class="form-control mt-1 block w-full rounded-md border-slate-300 bg-white text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                >
                    <option value="" disabled {{ old('origin_unit') ? '' : 'selected' }}>Pilih asal unit</option>
                    <option value="IGD" {{ old('origin_unit') === 'IGD' ? 'selected' : '' }}>IGD</option>
                    <option value="Bangsal" {{ old('origin_unit') === 'Bangsal' ? 'selected' : '' }}>Bangsal</option>
                    <option value="Poli" {{ old('origin_unit') === 'Poli' ? 'selected' : '' }}>Poli</option>
                </select>
                <x-input-error :messages="$errors->get('origin_unit')" class="mt-2" />
            </div>
        </div>

        <div class="mt-6 flex items-center justify-between gap-4">
            <a class="text-sm font-medium text-emerald-700 hover:text-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="btn-auth justify-center">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        (function () {
            const role = document.getElementById('role');
            const nurseType = document.getElementById('nurse_type');

            const doctorFields = document.getElementById('doctorFields');
            const nurseFields = document.getElementById('nurseFields');
            const unitFields = document.getElementById('unitFields');

            const doctorRequiredIds = ['specialist_id', 'title', 'str_number', 'sip_number'];
            const nurseRequiredIds = ['nurse_type'];
            const unitRequiredIds = ['origin_unit'];

            const setRequiredByIds = (ids, required) => {
                ids.forEach((id) => {
                    const el = document.getElementById(id);
                    if (!el) return;
                    if (required) el.setAttribute('required', 'required');
                    else el.removeAttribute('required');
                });
            };

            const setVisible = (el, visible) => {
                if (!el) return;
                el.style.display = visible ? '' : 'none';
                el.setAttribute('aria-hidden', visible ? 'false' : 'true');
            };

            const sync = () => {
                const selectedRole = role?.value || '';
                const selectedNurseType = nurseType?.value || '';

                const isDoctor = selectedRole === 'dokter';
                const isNurse = selectedRole === 'perawat';
                const isRegularNurse = isNurse && selectedNurseType === 'perawat_biasa';

                setVisible(doctorFields, isDoctor);
                setVisible(nurseFields, isNurse);
                setVisible(unitFields, isRegularNurse);

                setRequiredByIds(doctorRequiredIds, isDoctor);
                setRequiredByIds(nurseRequiredIds, isNurse);
                setRequiredByIds(unitRequiredIds, isRegularNurse);
            };

            // Init + listeners
            sync();
            role?.addEventListener('change', sync);
            nurseType?.addEventListener('change', sync);
        })();
    </script>
</x-guest-layout>
