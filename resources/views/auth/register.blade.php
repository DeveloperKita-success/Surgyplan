<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Buat Akun</h1>
        <p class="mt-2 text-sm leading-relaxed text-slate-600">Pilih peran Anda untuk melengkapi data yang diperlukan.</p>
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
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-input-label for="role" :value="__('Daftar Sebagai')" />
            <select
                id="role"
                name="role"
                class="mt-1 block w-full rounded-md border-slate-300 bg-white text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                required
            >
                <option value="" disabled {{ old('role') ? '' : 'selected' }}>Pilih jenis akun</option>
                <option value="dokter" {{ old('role') === 'dokter' ? 'selected' : '' }}>Dokter</option>
                <option value="perawat" {{ old('role') === 'perawat' ? 'selected' : '' }}>Perawat</option>
            </select>
            <p class="mt-2 text-xs text-slate-500">Jika memilih Perawat, pilih juga apakah Perawat UK atau Bukan.</p>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Doctor fields -->
        <div id="doctorFields" class="mt-4 space-y-4" aria-hidden="true">
            <div>
                <x-input-label for="specialist" :value="__('Spesialis')" />
                <x-text-input id="specialist" class="mt-1 block w-full" type="text" name="specialist" :value="old('specialist')" autocomplete="off" />
                <x-input-error :messages="$errors->get('specialist')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="degree" :value="__('Pangkat')" />
                <x-text-input id="degree" class="mt-1 block w-full" type="text" name="degree" :value="old('degree')" autocomplete="off" />
                <x-input-error :messages="$errors->get('degree')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="sip_number" :value="__('Nomor SIP / Izin Praktik')" />
                <x-text-input id="sip_number" class="mt-1 block w-full" type="text" name="sip_number" :value="old('sip_number')" autocomplete="off" />
                <x-input-error :messages="$errors->get('sip_number')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="address" :value="__('Alamat')" />
                <textarea
                    id="address"
                    name="address"
                    class="mt-1 block w-full rounded-md border-slate-300 bg-white text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                    rows="3"
                >{{ old('address') }}</textarea>
                <x-input-error :messages="$errors->get('address')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="education_history" :value="__('Riwayat Pendidikan')" />
                <textarea
                    id="education_history"
                    name="education_history"
                    class="mt-1 block w-full rounded-md border-slate-300 bg-white text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                    rows="3"
                >{{ old('education_history') }}</textarea>
                <x-input-error :messages="$errors->get('education_history')" class="mt-2" />
            </div>
        </div>

        <!-- Nurse fields -->
        <div id="nurseFields" class="mt-4 space-y-4" aria-hidden="true">
            <div>
                <x-input-label for="nurse_type" :value="__('Sebagai Perawat UK?')" />
                <select
                    id="nurse_type"
                    name="nurse_type"
                    class="mt-1 block w-full rounded-md border-slate-300 bg-white text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                >
                    <option value="" disabled {{ old('nurse_type') ? '' : 'selected' }}>Pilih opsi</option>
                    <option value="uk" {{ old('nurse_type') === 'uk' ? 'selected' : '' }}>Ya, Perawat UK</option>
                    <option value="biasa" {{ old('nurse_type') === 'biasa' ? 'selected' : '' }}>Bukan Perawat UK </option>
                </select>
                <x-input-error :messages="$errors->get('nurse_type')" class="mt-2" />
            </div>

            <div id="unitFields" aria-hidden="true">
                <x-input-label for="unit_asal" :value="__('Asal Unit')" />
                <select
                    id="unit_asal"
                    name="unit_asal"
                    class="mt-1 block w-full rounded-md border-slate-300 bg-white text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                >
                    <option value="" disabled {{ old('unit_asal') ? '' : 'selected' }}>Pilih asal unit</option>
                    <option value="IGD" {{ old('unit_asal') === 'IGD' ? 'selected' : '' }}>IGD</option>
                    <option value="Bangsal" {{ old('unit_asal') === 'Bangsal' ? 'selected' : '' }}>Bangsal</option>
                    <option value="Poli" {{ old('unit_asal') === 'Poli' ? 'selected' : '' }}>Poli</option>
                </select>
                <x-input-error :messages="$errors->get('unit_asal')" class="mt-2" />
            </div>
        </div>

        <div class="mt-6 flex items-center justify-between gap-4">
            <a class="text-sm font-medium text-emerald-700 hover:text-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="justify-center">
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

            const setVisible = (el, visible) => {
                if (!el) return;
                el.style.display = visible ? '' : 'none';
                el.setAttribute('aria-hidden', visible ? 'false' : 'true');

                // Only toggle required for inputs/selects inside this section.
                el.querySelectorAll('input, select, textarea').forEach((input) => {
                    if (visible) return;
                    input.removeAttribute('required');
                });
            };

            const sync = () => {
                const selectedRole = role?.value || '';
                const selectedNurseType = nurseType?.value || '';

                setVisible(doctorFields, selectedRole === 'dokter');
                setVisible(nurseFields, selectedRole === 'perawat');
                setVisible(unitFields, selectedRole === 'perawat' && selectedNurseType === 'biasa');
            };

            // Init + listeners
            sync();
            role?.addEventListener('change', sync);
            nurseType?.addEventListener('change', sync);
        })();
    </script>
</x-guest-layout>
