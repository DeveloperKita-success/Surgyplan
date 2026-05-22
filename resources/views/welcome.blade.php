<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'SurgyPlan') }} — Sistem Penjadwalan Operasi Pasien</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles & Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            @keyframes surgyplanDot {
                0%, 20% {
                    opacity: 0;
                }
                30%, 70% {
                    opacity: 1;
                }
                100% {
                    opacity: 0;
                }
            }

            .surgyplan-dot {
                display: inline-block;
                width: 0.35em;
                text-align: center;
                animation: surgyplanDot 1.2s infinite;
            }

            .surgyplan-dot--2 {
                animation-delay: 0.2s;
            }

            .surgyplan-dot--3 {
                animation-delay: 0.4s;
            }
        </style>
    </head>
    <body class="font-sans antialiased text-slate-900">
        <!-- Splash Screen (click to skip) -->
        <div
            id="splash"
            class="fixed inset-0 z-50 flex items-center justify-center bg-[#DAF1de] transition-opacity duration-500"
            aria-label="Splash screen SurgyPlan"
        >
            <div class="mx-auto w-full max-w-md px-6 text-center">
                <img
                    src="{{ asset('image/splash.png') }}"
                    alt="Logo SurgyPlan"
                    class="mx-auto w-full max-w-[32rem] h-auto"
                />
                <p class="mt-4 text-[20px] font-medium tracking-wide text-slate-700" style="font-family: 'Times New Roman', Times, serif;">
                    Precision in Every surgical schedule
                </p>
                <div class="mt-6" role="status" aria-live="polite">
                    <div class="inline-flex items-center gap-2 rounded-full bg-slate-50 px-4 py-2 text-sm text-slate-700 ring-1 ring-slate-200 motion-safe:animate-pulse">
                        <span
                            class="h-4 w-4 rounded-full border-2 border-slate-300 border-t-emerald-600 motion-safe:animate-spin motion-reduce:hidden"
                            aria-hidden="true"
                        ></span>
                        <span class="font-medium">
                            Memuat SurgyPlan
                            <span class="motion-safe:inline motion-reduce:hidden" aria-hidden="true">
                                <span class="surgyplan-dot surgyplan-dot--1">.</span><span class="surgyplan-dot surgyplan-dot--2">.</span><span class="surgyplan-dot surgyplan-dot--3">.</span>
                            </span>
                            <span class="motion-reduce:inline motion-safe:hidden" aria-hidden="true">...</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="min-h-screen bg-gradient-to-b from-emerald-50 via-white to-white">
            <header class="border-b border-slate-200/60 bg-white/80 backdrop-blur sp-reveal" data-reveal>
                <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-6 py-4">
                    <a href="{{ url('/') }}" class="flex items-center gap-3">
                        <img
                            src="{{ asset('image/LOGO-removebg-preview.png') }}"
                            alt="SurgyPlan"
                            class="h-10 w-auto"
                        />
                        <div class="leading-tight">
                            <div class="text-base font-semibold text-slate-900">SurgyPlan</div>
                            <div class="text-xs text-slate-600">Sistem Penjadwalan Operasi Pasien</div>
                        </div>
                    </a>

                    @if (Route::has('login'))
                        <nav class="flex items-center gap-2">
                            @auth
                                <a
                                    href="{{ url('/dashboard') }}"
                                    class="rounded-lg px-4 py-2 text-sm font-semibold text-slate-700 ring-1 ring-slate-200 transition hover:bg-slate-50 active:scale-95"
                                >
                                    Dashboard
                                </a>
                            @else
                                <a
                                    href="#fitur"
                                    class="hidden rounded-lg px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 active:scale-95 sm:inline-flex"
                                >
                                    Fitur
                                </a>
                                <a
                                    href="#alur"
                                    class="hidden rounded-lg px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 active:scale-95 sm:inline-flex"
                                >
                                    Alur
                                </a>

                                <a
                                    href="{{ route('login') }}"
                                    class="rounded-lg px-4 py-2 text-sm font-semibold text-emerald-700 ring-1 ring-emerald-200 transition hover:bg-emerald-50 active:scale-95"
                                >
                                    Masuk
                                </a>

                            @endauth
                        </nav>
                    @endif
                </div>
            </header>

            <main>
                <section class="mx-auto max-w-7xl px-6 pb-12 pt-12 sm:pt-16">
                    <div class="grid items-center gap-10 lg:grid-cols-2">
                        <div>
                            <p class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-800 ring-1 ring-emerald-100 sp-reveal" data-reveal data-reveal-delay="0">
                                Terstruktur · Transparan · Terkoordinasi
                            </p>

                            <h1 class="mt-5 text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl sp-reveal" data-reveal data-reveal-delay="80">
                                Smart Operating Room Management System
                            </h1>

                            <p class="mt-4 text-base leading-relaxed text-slate-600 sp-reveal" data-reveal data-reveal-delay="140">
                                SurgyPlan menghadirkan sistem manajemen jadwal operasi rumah sakit yang terintegrasi untuk membantu
                                tenaga medis mengatur penjadwalan operasi, koordinasi tim, serta monitoring pasien di Instansi Bedah
                                Sentral (IBS) secara lebih cepat, tepat, akurat, efisien.
                            </p>

                            <div class="mt-7 flex flex-col gap-3 sm:flex-row sm:items-center sp-reveal" data-reveal data-reveal-delay="220">
                                @if (Route::has('login'))
                                    <a
                                        href="{{ route('login') }}"
                                        class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 active:scale-95"
                                    >
                                        Mulai Sekarang
                                    </a>
                                @endif
                                <a
                                    href="#alur"
                                    class="inline-flex items-center justify-center rounded-xl px-5 py-3 text-sm font-semibold text-slate-700 ring-1 ring-slate-200 transition hover:bg-white active:scale-95"
                                >
                                    Lihat Alur Proses
                                </a>
                            </div>

                            <div class="mt-8 sp-reveal" data-reveal data-reveal-delay="300">
                                <div class="mb-3 flex items-center justify-between">
                                    <div class="text-sm font-semibold text-slate-900">Rekapan Pasien Operasi</div>
                                    <div class="text-xs text-slate-500">Ringkas per sumber pasien</div>
                                </div>

                                <div class="grid gap-4 sm:grid-cols-3">
                                <div class="rounded-xl bg-white p-4 ring-1 ring-slate-200 sp-hover-lift hover:shadow-md">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <div class="text-sm font-semibold text-slate-900">Rekapan Pasien IGD</div>
                                            <div class="mt-1 text-xs leading-relaxed text-slate-600">Total pasien operasi dari IGD</div>
                                        </div>
                                        <div class="text-lg font-semibold text-slate-900">
                                            {{ number_format(data_get($rekapPasien ?? [], 'igd', 0), 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="rounded-xl bg-white p-4 ring-1 ring-slate-200 sp-hover-lift hover:shadow-md">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <div class="text-sm font-semibold text-slate-900">Rekapan Pasien Bangsal</div>
                                            <div class="mt-1 text-xs leading-relaxed text-slate-600">Total pasien operasi dari bangsal</div>
                                        </div>
                                        <div class="text-lg font-semibold text-slate-900">
                                            {{ number_format(data_get($rekapPasien ?? [], 'bangsal', 0), 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="rounded-xl bg-white p-4 ring-1 ring-slate-200 sp-hover-lift hover:shadow-md">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <div class="text-sm font-semibold text-slate-900">Rekapan Pasien Poli</div>
                                            <div class="mt-1 text-xs leading-relaxed text-slate-600">Total pasien operasi dari poli</div>
                                        </div>
                                        <div class="text-lg font-semibold text-slate-900">
                                            {{ number_format(data_get($rekapPasien ?? [], 'poli', 0), 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>

                        <div class="relative sp-reveal" data-reveal data-reveal-delay="160">
                            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sp-hover-lift hover:shadow-md">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900">Ringkasan Proses</div>
                                        <div class="mt-1 text-xs text-slate-600">Alur inti untuk pengelolaan ruang operasi</div>
                                    </div>
                                    <div class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-800 ring-1 ring-emerald-100">
                                        SurgyPlan
                                    </div>
                                </div>

                                <div class="mt-6 grid gap-3">
                                    <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-4 ring-1 ring-slate-100 sp-hover-lift">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white text-sm font-semibold text-slate-700 ring-1 ring-slate-200">1</div>
                                        <div>
                                            <div class="text-sm font-semibold text-slate-900">Penjadwalan Operasi</div>
                                            <div class="text-xs text-slate-600">Menyusun jadwal berdasarkan kebutuhan & kesiapan</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-4 ring-1 ring-slate-100 sp-hover-lift">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white text-sm font-semibold text-slate-700 ring-1 ring-slate-200">2</div>
                                        <div>
                                            <div class="text-sm font-semibold text-slate-900">Booking Ruang Operasi</div>
                                            <div class="text-xs text-slate-600">Mengamankan slot ruang & sumber daya terkait</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-4 ring-1 ring-slate-100 sp-hover-lift">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white text-sm font-semibold text-slate-700 ring-1 ring-slate-200">3</div>
                                        <div>
                                            <div class="text-sm font-semibold text-slate-900">Prioritas & Emergency</div>
                                            <div class="text-xs text-slate-600">Menangani kasus prioritas tanpa kehilangan kontrol</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-4 ring-1 ring-slate-100 sp-hover-lift">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white text-sm font-semibold text-slate-700 ring-1 ring-slate-200">4</div>
                                        <div>
                                            <div class="text-sm font-semibold text-slate-900">Monitoring Status Operasi</div>
                                            <div class="text-xs text-slate-600">Memantau progres operasi dan kondisi pasien</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="fitur" class="mx-auto max-w-7xl px-6 py-12">
                    <div class="max-w-2xl">
                        <h2 class="text-2xl font-semibold tracking-tight text-slate-900 sp-reveal" data-reveal>Fitur Utama</h2>
                        <p class="mt-3 text-sm leading-relaxed text-slate-600 sp-reveal" data-reveal data-reveal-delay="80">
                            Fitur inti untuk mendukung penjadwalan, booking ruang operasi, penanganan prioritas/emergency, dan monitoring status operasi.
                        </p>
                    </div>

                    <div class="mt-8 grid gap-4 lg:grid-cols-4">
                        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sp-reveal sp-hover-lift hover:shadow-md" data-reveal data-reveal-delay="0">
                            <div class="text-sm font-semibold text-slate-900">Penjadwalan Operasi</div>
                            <p class="mt-2 text-sm leading-relaxed text-slate-600">
                                Menyusun jadwal operasi secara terstruktur untuk mendukung koordinasi tim dan kesiapan tindakan.
                            </p>
                        </div>
                        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sp-reveal sp-hover-lift hover:shadow-md" data-reveal data-reveal-delay="80">
                            <div class="text-sm font-semibold text-slate-900">Booking Ruang Operasi</div>
                            <p class="mt-2 text-sm leading-relaxed text-slate-600">
                                Mengelola pemesanan ruang operasi dan ketersediaan sumber daya agar jadwal tetap terkendali.
                            </p>
                        </div>
                        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sp-reveal sp-hover-lift hover:shadow-md" data-reveal data-reveal-delay="160">
                            <div class="text-sm font-semibold text-slate-900">Prioritas & Emergency</div>
                            <p class="mt-2 text-sm leading-relaxed text-slate-600">
                                Mendukung penentuan prioritas dan penanganan kasus emergency secara cepat tanpa mengganggu alur.
                            </p>
                        </div>
                        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sp-reveal sp-hover-lift hover:shadow-md" data-reveal data-reveal-delay="240">
                            <div class="text-sm font-semibold text-slate-900">Monitoring Status Operasi</div>
                            <p class="mt-2 text-sm leading-relaxed text-slate-600">
                                Memantau status operasi dan kondisi pasien di IBS secara real-time agar informasi tetap akurat.
                            </p>
                        </div>
                    </div>
                </section>

                <section id="alur" class="mx-auto max-w-7xl px-6 py-12">
                    <div class="rounded-3xl bg-white p-8 ring-1 ring-slate-200 sm:p-10">
                        <div class="max-w-2xl">
                            <h2 class="text-2xl font-semibold tracking-tight text-slate-900 sp-reveal" data-reveal>Alur Proses SurgyPlan</h2>
                            <p class="mt-3 text-sm leading-relaxed text-slate-600 sp-reveal" data-reveal data-reveal-delay="80">
                                Alur kerja yang konsisten untuk mempercepat koordinasi tim dan memastikan jadwal serta status operasi mudah dipantau.
                            </p>
                        </div>

                        <div class="mt-8 grid gap-4 lg:grid-cols-2">
                            <div class="rounded-2xl bg-slate-50 p-6 ring-1 ring-slate-100 sp-reveal sp-hover-lift" data-reveal data-reveal-delay="0">
                                <div class="flex items-start gap-4">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-sm font-semibold text-emerald-700 ring-1 ring-slate-200">01</div>
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900">Penjadwalan operasi dibuat</div>
                                        <p class="mt-1 text-sm text-slate-600">Jadwal disusun terstruktur untuk mendukung koordinasi tim.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-6 ring-1 ring-slate-100 sp-reveal sp-hover-lift" data-reveal data-reveal-delay="80">
                                <div class="flex items-start gap-4">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-sm font-semibold text-emerald-700 ring-1 ring-slate-200">02</div>
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900">Booking ruang operasi</div>
                                        <p class="mt-1 text-sm text-slate-600">Slot ruang dan kebutuhan sumber daya dikelola dalam satu sistem.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-6 ring-1 ring-slate-100 sp-reveal sp-hover-lift" data-reveal data-reveal-delay="160">
                                <div class="flex items-start gap-4">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-sm font-semibold text-emerald-700 ring-1 ring-slate-200">03</div>
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900">Prioritas & emergency ditangani</div>
                                        <p class="mt-1 text-sm text-slate-600">Kasus prioritas dapat masuk tanpa membuat alur operasional kacau.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-6 ring-1 ring-slate-100 sp-reveal sp-hover-lift" data-reveal data-reveal-delay="240">
                                <div class="flex items-start gap-4">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-sm font-semibold text-emerald-700 ring-1 ring-slate-200">04</div>
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900">Monitoring status operasi</div>
                                        <p class="mt-1 text-sm text-slate-600">Status operasi dan kondisi pasien dipantau agar informasi selalu akurat.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="mx-auto max-w-7xl px-6 pb-16 pt-4">
                    <div class="rounded-3xl bg-emerald-600 p-8 text-white shadow-sm sm:p-10 sp-reveal" data-reveal>
                        <div class="grid gap-8 lg:grid-cols-2 lg:items-center">
                            <div>
                                <h2 class="text-2xl font-semibold tracking-tight">Siap meningkatkan efisiensi IBS?</h2>
                                <p class="mt-3 text-sm leading-relaxed text-white/90">
                                    SurgyPlan membantu penjadwalan, booking ruang, penanganan prioritas/emergency, dan monitoring status operasi tetap rapi, jelas, dan mudah ditelusuri.
                                </p>
                            </div>
                            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                                @if (Route::has('login'))
                                    <a
                                        href="{{ route('login') }}"
                                        class="inline-flex items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50 active:scale-95"
                                    >
                                        Masuk
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </section>
            </main>

            <footer class="border-t border-slate-200/60 bg-white">
                <div class="mx-auto flex max-w-7xl flex-col gap-2 px-6 py-8 text-sm text-slate-600 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <span class="font-semibold text-slate-900">SurgyPlan</span>
                        <span class="text-slate-600">· Sistem Penjadwalan Operasi Pasien</span>
                    </div>
                    <div class="text-xs text-slate-500">
                        © {{ date('Y') }} · {{ config('app.name', 'SurgyPlan') }}
                    </div>
                </div>
            </footer>
        </div>

        <script>
            (function () {
                const initReveals = () => {
                    const elements = Array.from(document.querySelectorAll('[data-reveal].sp-reveal'));
                    if (!elements.length) return;

                    const prefersReducedMotion =
                        window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

                    const makeVisible = (el) => {
                        if (el.classList.contains('is-visible')) return;

                        const delay = Number(el.getAttribute('data-reveal-delay') || '0');
                        if (!Number.isNaN(delay) && delay > 0) {
                            el.style.transitionDelay = `${delay}ms`;
                        }
                        el.classList.add('is-visible');
                    };

                    if (prefersReducedMotion || !('IntersectionObserver' in window)) {
                        elements.forEach(makeVisible);
                        return;
                    }

                    const observer = new IntersectionObserver(
                        (entries) => {
                            entries.forEach((entry) => {
                                if (!entry.isIntersecting) return;
                                makeVisible(entry.target);
                                observer.unobserve(entry.target);
                            });
                        },
                        {
                            threshold: 0.12,
                            rootMargin: '0px 0px -10% 0px',
                        }
                    );

                    elements.forEach((el) => observer.observe(el));

                    // Above-the-fold elements should feel snappy.
                    window.requestAnimationFrame(() => {
                        elements
                            .filter((el) => el.getBoundingClientRect().top < window.innerHeight * 0.95)
                            .forEach(makeVisible);
                    });
                };

                const splash = document.getElementById('splash');
                if (!splash) {
                    initReveals();
                    return;
                }

                document.body.classList.add('overflow-hidden');

                const prefersReducedMotion =
                    window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

                const removeSplash = () => {
                    document.body.classList.remove('overflow-hidden');
                    splash.remove();
                    window.requestAnimationFrame(initReveals);
                };

                const hideSplash = () => {
                    if (prefersReducedMotion) {
                        removeSplash();
                        return;
                    }

                    splash.classList.add('opacity-0');
                    splash.addEventListener('transitionend', removeSplash, { once: true });
                };

                window.setTimeout(hideSplash, 5000);
                splash.addEventListener('click', hideSplash, { once: true });
            })();
        </script>
    </body>
</html>
