<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SurgyPlan') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="relative min-h-screen overflow-x-hidden bg-gradient-to-br from-green-50 to-emerald-100 font-sans antialiased text-slate-900">
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -left-28 -top-24 h-96 w-96 rounded-full bg-white/40 blur-3xl"></div>
        <div class="absolute -right-32 top-10 h-[28rem] w-[28rem] rounded-full bg-emerald-200/40 blur-3xl"></div>
        <div class="absolute left-1/2 top-24 h-56 w-56 -translate-x-1/2 rounded-full bg-green-200/30 blur-3xl"></div>
        <div class="absolute -bottom-40 left-10 h-[32rem] w-[32rem] rounded-[6rem] bg-white/30 blur-3xl"></div>
        <div class="absolute left-12 top-16 h-24 w-24 rounded-full border border-emerald-900/10"></div>
        <div class="absolute right-24 top-36 h-40 w-40 rounded-full border border-emerald-900/10"></div>
    </div>
    @php
        $user = auth()->user();
        $roleLabel = match ($user?->role) {
            \App\Models\User::ROLE_DOKTER => 'Dokter',
            \App\Models\User::ROLE_PERAWAT_UK => 'Perawat OK',
            \App\Models\User::ROLE_PERAWAT_BIASA => 'Perawat Reguler',
            \App\Models\User::ROLE_ADMIN => 'Admin',
            default => 'Pengguna',
        };

        $sidebarView = match ($user?->role) {
            \App\Models\User::ROLE_DOKTER => 'layouts.sidebars.doctor',
            \App\Models\User::ROLE_PERAWAT_UK => 'layouts.sidebars.nurse-uk',
            \App\Models\User::ROLE_PERAWAT_BIASA => 'layouts.sidebars.nurse-regular',
            \App\Models\User::ROLE_ADMIN => 'layouts.sidebars.admin',
            default => null,
        };
    @endphp

    <div x-data="{ sidebarOpen: false }" @sidebar-open.window="sidebarOpen = true" class="relative z-10 min-h-screen">
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-30 bg-slate-950/45 lg:hidden"
            @click="sidebarOpen = false"></div>

        <aside
            class="fixed left-0 top-0 z-40 flex h-screen w-64 -translate-x-[110%] flex-col overflow-hidden shadow-xl transition duration-300 ease-in-out lg:translate-x-0"
            :class="{ 'translate-x-0': sidebarOpen }">
            <div class="absolute inset-0 bg-gradient-to-b from-emerald-900 via-emerald-800 to-green-600"></div>
            <div class="absolute inset-0 bg-white/10 backdrop-blur-xl"></div>

            <div class="pointer-events-none absolute inset-0 overflow-hidden">
                <div class="absolute -right-16 -top-16 h-56 w-56 rounded-full border border-white/10"></div>
                <div class="absolute -left-24 top-28 h-72 w-72 rounded-full border border-white/10"></div>
                <div class="absolute -bottom-16 right-10 h-64 w-64 rounded-[4rem] bg-white/10 blur-3xl opacity-30">
                </div>

                <div class="absolute bottom-6 left-6 grid grid-cols-6 gap-2 opacity-20">
                    @for ($i = 0; $i < 18; $i++)
                        <span class="h-1 w-1 rounded-full bg-white"></span>
                    @endfor
                </div>
            </div>

            <div class="relative flex h-full flex-col p-4">
                <div class="flex items-center gap-3 px-2 py-2">
                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-white/15 shadow-md ring-1 ring-white/15">
                        <img src="{{ asset('image/splash.png') }}" alt="Logo SurgyPlan" class="h-8 w-8 object-contain" />
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-base font-bold tracking-tight text-white">SurgyPlan</p>
                        <p class="truncate text-xs font-medium text-green-100/90">Hospital Dashboard</p>
                    </div>
                </div>

                <div class="mt-3 rounded-2xl bg-white/10 p-3 backdrop-blur-md ring-1 ring-white/15">
                    <p class="truncate text-sm font-semibold text-white">{{ $user?->name }}</p>
                    <p class="mt-1 text-xs font-medium text-green-100/90">{{ $roleLabel }}</p>

                    <div class="mt-3 flex items-center gap-2">
                        <a href="{{ route('profile.edit') }}"
                            class="inline-flex flex-1 items-center justify-center rounded-xl bg-white/10 px-3 py-2 text-xs font-semibold text-white transition-all duration-300 ease-in-out hover:bg-white/20">
                            Pengaturan
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="flex-1">
                            @csrf
                            <button type="submit"
                                class="inline-flex w-full items-center justify-center rounded-xl bg-white/10 px-3 py-2 text-xs font-semibold text-white transition-all duration-300 ease-in-out hover:bg-white/20">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>

                @if ($sidebarView)
                    <div class="min-h-0 flex-1 overflow-y-auto pr-1 pt-4">
                        @include($sidebarView)
                    </div>
                @endif
            </div>
        </aside>

        @include('layouts.navigation')

        <main class="ml-0 p-6 pt-20 sm:p-8 sm:pt-20 lg:ml-64">
            @isset($header)
                <div class="mb-6">
                    {{ $header }}
                </div>
            @endisset

            {{ $slot }}
        </main>
    </div>
</body>

</html>
