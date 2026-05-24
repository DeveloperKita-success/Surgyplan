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

<body class="relative min-h-screen overflow-x-hidden bg-[#f8faf9] font-sans antialiased text-slate-900">
    @php
        $user = auth()->user();
        $roleLabel = match ($user?->role) {
            \App\Models\User::ROLE_DOKTER => 'Dokter',
            \App\Models\User::ROLE_PERAWAT_OK => 'Perawat OK',
            \App\Models\User::ROLE_PERAWAT_BIASA => 'Perawat Reguler',
            \App\Models\User::ROLE_ADMIN => 'Admin',
            default => 'Pengguna',
        };

        $sidebarView = match ($user?->role) {
            \App\Models\User::ROLE_DOKTER => 'layouts.sidebars.doctor',
            \App\Models\User::ROLE_PERAWAT_OK => 'layouts.sidebars.nurse-ok',
            \App\Models\User::ROLE_PERAWAT_BIASA => 'layouts.sidebars.nurse-regular',
            \App\Models\User::ROLE_ADMIN => 'layouts.sidebars.admin',
            default => null,
        };
    @endphp

    <div x-data="{ sidebarOpen: false }" @sidebar-open.window="sidebarOpen = true" class="relative z-10 min-h-screen">
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-30 bg-slate-950/45 lg:hidden"
            @click="sidebarOpen = false"></div>

        <aside
            class="fixed bottom-0 left-0 top-16 z-40 flex w-[min(18rem,86vw)] -translate-x-[110%] flex-col overflow-hidden rounded-tr-[2rem] shadow-[18px_0_45px_rgba(6,53,47,0.12)] transition duration-300 ease-in-out sm:top-[72px] sm:w-72 lg:translate-x-0"
            :class="{ 'translate-x-0': sidebarOpen }">
            <div class="absolute inset-0 bg-gradient-to-b from-[#052f2a] via-[#0a433b] to-[#0f5c4d]"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950/12 via-transparent to-white/5 backdrop-blur-xl"></div>

            <div class="pointer-events-none absolute inset-0 overflow-hidden">
                <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full border border-white/10 bg-white/5"></div>
                <div class="absolute -left-24 top-28 h-72 w-72 rounded-full border border-white/10"></div>
                <div class="absolute -bottom-16 right-10 h-64 w-64 rounded-[4rem] bg-emerald-200/12 blur-3xl opacity-40">
                </div>

                <div class="absolute bottom-6 left-6 grid grid-cols-6 gap-2 opacity-20">
                    @for ($i = 0; $i < 18; $i++)
                        <span class="h-1 w-1 rounded-full bg-white"></span>
                    @endfor
                </div>
            </div>

            <div class="relative flex h-full flex-col p-4 sm:p-6">
                @if ($sidebarView)
                    <div class="min-h-0 flex-1 overflow-y-auto pr-1 pt-3">
                        @include($sidebarView)
                    </div>
                @endif

                <div class="mt-auto rounded-3xl bg-white/[0.11] p-3 backdrop-blur-md ring-1 ring-white/12 shadow-[inset_0_1px_0_rgba(255,255,255,0.12)]">
                    <div class="flex items-center gap-3">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-sm font-bold text-teal-950">
                            {{
                                collect(explode(' ', $user?->name ?? 'U'))
                                    ->map(fn ($part) => mb_substr($part, 0, 1))
                                    ->take(2)
                                    ->implode('')
                            }}
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-white">{{ $user?->name }}</p>
                            <p class="mt-1 text-xs font-medium text-emerald-100/90">{{ $roleLabel }}</p>
                        </div>
                    </div>

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
            </div>
        </aside>

        @include('layouts.navigation')

        <main class="relative ml-0 min-h-screen overflow-hidden bg-[#f8faf9] p-4 pt-20 sm:p-9 sm:pt-28 lg:ml-72 lg:pl-10">
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute inset-0 bg-gradient-to-br from-white/92 via-[#f8faf9]/96 to-[#f4f6f5]/96"></div>
            </div>
            <div class="relative z-10">
            @isset($header)
                <div class="mb-6">
                    {{ $header }}
                </div>
            @endisset

            {{ $slot }}
            </div>
        </main>
    </div>
</body>

</html>
