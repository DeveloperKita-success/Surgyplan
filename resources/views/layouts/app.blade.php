<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SurgyPlan') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-900">
        @php
            $user = auth()->user();
            $roleLabel = match ($user?->role) {
                \App\Models\User::ROLE_DOKTER => 'Dokter',
                \App\Models\User::ROLE_PERAWAT_UK => 'Perawat UK',
                \App\Models\User::ROLE_PERAWAT_BIASA => 'Perawat Reguler',
                default => 'Pengguna',
            };

            $sidebarView = match ($user?->role) {
                \App\Models\User::ROLE_DOKTER => 'layouts.sidebars.doctor',
                \App\Models\User::ROLE_PERAWAT_UK => 'layouts.sidebars.nurse-uk',
                \App\Models\User::ROLE_PERAWAT_BIASA => 'layouts.sidebars.nurse-regular',
                default => null,
            };
        @endphp

        <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-slate-100">
            <div
                x-show="sidebarOpen"
                x-transition.opacity
                class="fixed inset-0 z-30 bg-slate-950/45 lg:hidden"
                @click="sidebarOpen = false"
            ></div>

            <aside
                class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col border-r border-slate-200 bg-white transition duration-200 lg:translate-x-0"
                :class="{ 'translate-x-0': sidebarOpen }"
            >
                <div class="flex h-20 items-center gap-3 border-b border-slate-200 px-6">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-cyan-700 shadow-sm">
                        <img
                            src="{{ asset('image/splash.png') }}"
                            alt="Logo SurgyPlan"
                            class="h-9 w-9 object-contain"
                        />
                    </div>
                    <div>
                        <p class="text-lg font-bold leading-tight text-slate-900">SurgyPlan</p>
                        <p class="text-sm text-slate-500">Sistem Operasi</p>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto px-4 py-5">
                    @if ($sidebarView)
                        @include($sidebarView)
                    @endif
                </div>

                <div class="border-t border-slate-200 p-4">
                    <div class="rounded-lg bg-slate-50 p-4">
                        <p class="truncate text-sm font-semibold text-slate-900">{{ $user?->name }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $roleLabel }}</p>
                    </div>
                </div>
            </aside>

            <div class="lg:pl-72">
                <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 backdrop-blur">
                    <div class="flex h-20 items-center gap-4 px-4 sm:px-6 lg:px-8">
                        <button
                            type="button"
                            class="inline-flex h-11 w-11 items-center justify-center rounded-lg border border-slate-200 text-slate-600 lg:hidden"
                            @click="sidebarOpen = true"
                            aria-label="Buka menu"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                <path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </button>

                        <div class="min-w-0 flex-1">
                            @isset($header)
                                {{ $header }}
                            @else
                                <h1 class="text-xl font-semibold text-slate-900">Dashboard</h1>
                            @endisset
                        </div>

                        <label class="hidden min-w-[280px] items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-slate-500 md:flex">
                            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none">
                                <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
                                <path d="m20 20-3.5-3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            <input type="search" placeholder="Cari pasien, jadwal, atau laporan" class="w-full border-0 bg-transparent p-0 text-sm text-slate-700 placeholder:text-slate-400 focus:ring-0">
                        </label>

                        <button type="button" class="relative inline-flex h-11 w-11 items-center justify-center rounded-lg border border-slate-200 text-slate-600" aria-label="Notifikasi">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            <span class="absolute right-2 top-2 h-2.5 w-2.5 rounded-full bg-rose-500"></span>
                        </button>

                        <div x-data="{ open: false }" class="relative">
                            <button
                                type="button"
                                class="flex items-center gap-3 rounded-lg border border-slate-200 px-3 py-2 text-left"
                                @click="open = ! open"
                            >
                                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-cyan-100 text-sm font-bold text-cyan-800">
                                    {{ collect(explode(' ', $user?->name ?? 'U'))->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->implode('') }}
                                </span>
                                <span class="hidden sm:block">
                                    <span class="block max-w-40 truncate text-sm font-semibold text-slate-900">{{ $user?->name }}</span>
                                    <span class="block text-xs text-slate-500">{{ $roleLabel }}</span>
                                </span>
                            </button>

                            <div
                                x-show="open"
                                x-transition
                                @click.outside="open = false"
                                class="absolute right-0 mt-3 w-52 rounded-lg border border-slate-200 bg-white p-2 shadow-lg"
                            >
                                <a href="{{ route('profile.edit') }}" class="block rounded-md px-3 py-2 text-sm text-slate-700 hover:bg-slate-100">
                                    Profil
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="mt-1 block w-full rounded-md px-3 py-2 text-left text-sm text-rose-600 hover:bg-rose-50">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="px-4 py-6 sm:px-6 lg:px-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
