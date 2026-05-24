@php
    $initialMode = $initialMode ?? 'login';
    $errorBag = $errors ?? new \Illuminate\Support\ViewErrorBag;
@endphp

<div
    x-data="modernAuth('{{ $initialMode }}')"
    x-init="init()"
    class="relative min-h-screen overflow-hidden bg-[#edf7f5] font-sans text-slate-900"
>
    <div class="pointer-events-none absolute inset-0">
        <div class="absolute -left-28 top-10 h-72 w-72 rounded-full bg-teal-200/45 blur-3xl"></div>
        <div class="absolute right-0 top-0 h-96 w-96 rounded-full bg-emerald-100/80 blur-3xl"></div>
        <div class="absolute bottom-0 left-1/3 h-80 w-80 rounded-full bg-cyan-100/70 blur-3xl"></div>
    </div>

    <main class="relative mx-auto flex min-h-screen w-full max-w-6xl items-center justify-center px-4 py-4 sm:px-6 lg:px-8">
        <section
            class="grid w-full overflow-hidden rounded-[2rem] border border-white/70 bg-white/65 shadow-[0_30px_90px_rgba(15,118,110,0.18)] backdrop-blur-xl lg:h-[calc(100vh-2rem)] lg:min-h-[560px] lg:max-h-[660px] lg:grid-cols-2"
            :class="isForgot ? 'lg:[&_.auth-form-panel]:translate-x-full lg:[&_.auth-visual-panel]:-translate-x-full' : ''"
        >
            <div class="auth-form-panel relative z-10 flex min-h-[560px] flex-col bg-white/90 px-6 py-6 transition-transform duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] sm:px-9 lg:min-h-0 lg:px-12">
                <a href="/" class="inline-flex w-max items-center gap-3">
                    <span class="grid h-14 w-14 place-items-center">
                        <img src="{{ asset('image/splash.png') }}" alt="HealthCare" class="h-14 w-14 object-contain">
                    </span>
                    <span>
                        <span class="block text-base font-bold tracking-normal text-teal-950">Surgyplan</span>
                        <span class="block text-xs font-medium text-slate-500">Hospital Management System</span>
                    </span>
                </a>

                @if (session('status'))
                    <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errorBag->any())
                    <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">
                        {{ $errorBag->first() }}
                    </div>
                @endif

                <div class="relative flex flex-1 items-center py-5">
                    <div class="relative -mx-2 w-[calc(100%+1rem)] overflow-visible px-2">
                        <form
                            method="POST"
                            action="{{ route('login') }}"
                            class="w-full space-y-4 py-1 transition-all duration-500 ease-out"
                            :class="isForgot ? 'pointer-events-none -translate-x-10 scale-[0.98] opacity-0 absolute inset-x-0 top-0' : 'relative translate-x-0 scale-100 opacity-100'"
                        >
                            @csrf

                            <div class="mb-6">
                                <h1 class="text-2xl font-bold tracking-normal text-slate-950 sm:text-3xl">Selamat Datang Kembali</h1>
                                <p class="mt-2 text-sm leading-6 text-slate-500">Silakan masuk ke akun Anda untuk melanjutkan</p>
                            </div>

                            <div class="px-1">
                                <label for="email" class="text-sm font-semibold text-slate-700">Email</label>
                                <div class="relative mt-2 rounded-[1.25rem]">
                                    <span class="pointer-events-none absolute inset-y-0 left-0 flex w-12 items-center justify-center text-slate-400">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M4.75 6.75h14.5v10.5H4.75V6.75Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                                            <path d="m5.25 7.25 6.75 5.5 6.75-5.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="h-[52px] w-full rounded-[1.25rem] border border-slate-200 bg-slate-50/80 py-3 pl-12 pr-4 text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-teal-600 focus:bg-white focus:ring-4 focus:ring-teal-600/15" placeholder="nama@email.com">
                                </div>
                                <x-input-error :messages="$errorBag->get('email')" class="mt-2" />
                            </div>

                            <div class="px-1">
                                <label for="password" class="text-sm font-semibold text-slate-700">Password</label>
                                <div class="relative mt-2 rounded-[1.25rem]">
                                    <span class="pointer-events-none absolute inset-y-0 left-0 flex w-12 items-center justify-center text-slate-400">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M7.75 10.25v-2.1a4.25 4.25 0 0 1 8.5 0v2.1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                            <path d="M6.25 10.25h11.5v8.5H6.25v-8.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                    <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password" class="h-[52px] w-full rounded-[1.25rem] border border-slate-200 bg-slate-50/80 py-3 pl-12 pr-12 text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-teal-600 focus:bg-white focus:ring-4 focus:ring-teal-600/15" placeholder="Masukkan password">
                                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex w-12 items-center justify-center rounded-r-2xl text-slate-400 transition hover:text-teal-700" aria-label="Tampilkan atau sembunyikan password">
                                        <svg x-show="!showPassword" class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M3.75 12s2.9-5.25 8.25-5.25S20.25 12 20.25 12 17.35 17.25 12 17.25 3.75 12 3.75 12Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                                            <path d="M12 14.25a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Z" stroke="currentColor" stroke-width="1.8" />
                                        </svg>
                                        <svg x-show="showPassword" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="m4 4 16 16M9.9 6.95A7.7 7.7 0 0 1 12 6.75c5.35 0 8.25 5.25 8.25 5.25a13.8 13.8 0 0 1-2.35 2.9M6.7 8.35A14 14 0 0 0 3.75 12S6.65 17.25 12 17.25c.98 0 1.88-.18 2.7-.48" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                </div>
                                <x-input-error :messages="$errorBag->get('password')" class="mt-2" />
                            </div>

                            <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                                <label for="remember_me" class="inline-flex items-center gap-2 text-sm font-medium text-slate-600">
                                    <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-teal-700 shadow-sm focus:ring-teal-600" name="remember">
                                    <span>Remember me</span>
                                </label>

                                <button type="button" @click="setMode('forgot')" class="text-sm font-semibold text-teal-800 transition hover:text-teal-950">
                                    Lupa kata sandi?
                                </button>
                            </div>

                            <button type="submit" class="flex h-12 w-full items-center justify-center rounded-2xl bg-teal-900 px-5 text-sm font-bold text-white shadow-xl shadow-teal-900/20 transition hover:-translate-y-0.5 hover:bg-teal-800 focus:outline-none focus:ring-4 focus:ring-teal-700/20">
                                Masuk
                            </button>
                        </form>

                        <form
                            method="POST"
                            action="{{ route('password.email') }}"
                            class="w-full space-y-4 py-1 transition-all duration-500 ease-out"
                            :class="isForgot ? 'relative translate-x-0 scale-100 opacity-100' : 'pointer-events-none absolute inset-x-0 top-0 translate-x-10 scale-[0.98] opacity-0'"
                        >
                            @csrf

                            <div class="mb-6">
                                <div class="mb-5 grid h-14 w-14 place-items-center rounded-3xl bg-teal-50 text-teal-800 shadow-inner">
                                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M7.75 10.25v-2.1a4.25 4.25 0 0 1 8.5 0v2.1" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" />
                                        <path d="M6.25 10.25h11.5v8.5H6.25v-8.5Z" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round" />
                                        <path d="M12 13.25v2.1" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" />
                                    </svg>
                                </div>
                                <h2 class="text-2xl font-bold tracking-normal text-slate-950 sm:text-3xl">Lupa Kata Sandi?</h2>
                                <p class="mt-2 max-w-md text-sm leading-6 text-slate-500">Masukkan email Anda dan kami akan mengirimkan tautan untuk mereset kata sandi.</p>
                            </div>

                            <div class="px-1">
                                <label for="reset_email" class="text-sm font-semibold text-slate-700">Email</label>
                                <div class="relative mt-2 rounded-[1.25rem]">
                                    <span class="pointer-events-none absolute inset-y-0 left-0 flex w-12 items-center justify-center text-slate-400">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M4.75 6.75h14.5v10.5H4.75V6.75Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                                            <path d="m5.25 7.25 6.75 5.5 6.75-5.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                    <input id="reset_email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="h-[52px] w-full rounded-[1.25rem] border border-slate-200 bg-slate-50/80 py-3 pl-12 pr-4 text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-teal-600 focus:bg-white focus:ring-4 focus:ring-teal-600/15" placeholder="nama@email.com">
                                </div>
                                <x-input-error :messages="$errorBag->get('email')" class="mt-2" />
                            </div>

                            <button type="submit" class="flex h-12 w-full items-center justify-center rounded-2xl bg-teal-900 px-5 text-sm font-bold text-white shadow-xl shadow-teal-900/20 transition hover:-translate-y-0.5 hover:bg-teal-800 focus:outline-none focus:ring-4 focus:ring-teal-700/20">
                                Kirim Tautan Reset
                            </button>

                            <button type="button" @click="setMode('login')" class="mx-auto block text-sm font-semibold text-teal-800 transition hover:text-teal-950">
                                Kembali ke halaman masuk
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="auth-visual-panel relative m-3 min-h-[320px] overflow-hidden rounded-[1.5rem] bg-teal-950 transition-transform duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] lg:ml-0 lg:min-h-0">
                <img src="{{ asset('image/BG.jpeg') }}" alt="Ruang operasi modern" class="absolute inset-0 h-full w-full object-cover transition duration-700" :class="isForgot ? 'scale-105' : 'scale-100'">
                <div class="absolute inset-0 bg-gradient-to-br from-teal-950/75 via-teal-900/35 to-emerald-500/20"></div>
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_28%_22%,rgba(255,255,255,0.24),transparent_34%)]"></div>

                <div class="relative flex h-full min-h-[320px] flex-col justify-between p-6 text-white sm:p-8 lg:min-h-0 lg:p-8">
                    <div class="flex items-center justify-between gap-4">
                        <div class="rounded-full border border-white/25 bg-white/15 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-white/85 backdrop-blur-md">Surgery</div>
                        <div class="h-12 w-12 rounded-2xl border border-white/20 bg-white/15 backdrop-blur-md"></div>
                    </div>

                    <div class="max-w-md rounded-[1.75rem] border border-white/20 bg-white/15 p-5 shadow-2xl shadow-teal-950/25 backdrop-blur-md">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-lg font-semibold text-white" x-text="dayName">Selasa</p>
                                <p class="mt-1 text-sm font-medium text-white/75" x-text="dateText">21 Mei 2024</p>
                            </div>
                            <p class="text-3xl font-bold tracking-normal text-white sm:text-4xl" x-text="timeText">19:34 PM</p>
                        </div>

                        <div class="my-5 h-px w-full bg-gradient-to-r from-white/75 via-white/25 to-transparent"></div>

                        <p class="text-base font-medium leading-7 text-white/90">
                            "Kesehatan adalah mahkota yang hanya dilihat oleh orang sakit."
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>

<script>
    function modernAuth(initialMode) {
        return {
            mode: initialMode === 'forgot' ? 'forgot' : 'login',
            showPassword: false,
            dayName: '',
            dateText: '',
            timeText: '',
            timer: null,
            get isForgot() {
                return this.mode === 'forgot';
            },
            init() {
                this.updateClock();
                this.timer = setInterval(() => this.updateClock(), 1000);
            },
            setMode(nextMode) {
                this.mode = nextMode;
                window.history.replaceState({}, '', nextMode === 'forgot' ? '{{ route('password.request') }}' : '{{ route('login') }}');
            },
            updateClock() {
                const now = new Date();
                this.dayName = new Intl.DateTimeFormat('id-ID', { weekday: 'long' }).format(now);
                this.dateText = new Intl.DateTimeFormat('id-ID', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                }).format(now);
                this.timeText = new Intl.DateTimeFormat('en-US', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                }).format(now);
            }
        }
    }
</script>
