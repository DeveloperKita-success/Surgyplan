<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SurgyPlan') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-900">
        <div class="min-h-screen bg-gradient-to-b from-emerald-50 via-white to-white">
            <div class="mx-auto flex min-h-screen max-w-7xl flex-col items-center justify-center px-6 py-10">
                <a href="/" class="flex flex-col items-center gap-3">
                    <x-application-logo class="h-16 w-auto" />
                    <div class="text-center leading-tight">
                        <div class="text-base font-semibold text-slate-900">{{ config('app.name', 'SurgyPlan') }}</div>
                        <div class="text-xs text-slate-600">Sistem Penjadwalan Operasi Pasien</div>
                    </div>
                </a>

                <div class="mt-6 w-full sm:max-w-md overflow-hidden rounded-2xl bg-white p-8 shadow-sm ring-1 ring-slate-200">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
