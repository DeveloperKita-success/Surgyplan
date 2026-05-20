@php
    $items = [
        ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'nurse-uk.dashboard', 'active' => 'nurse-uk.dashboard'],
        ['label' => 'Data Patient', 'icon' => 'user', 'route' => 'nurse-regular.patients.index', 'active' => 'nurse-regular.patients.*'],
        ['label' => 'Pengajuan Operasi', 'icon' => 'clipboard', 'route' => 'nurse-uk.requests.index', 'active' => 'nurse-uk.requests.*'],
        ['label' => 'Jadwal Operasi', 'icon' => 'calendar', 'route' => 'nurse-uk.schedules.index', 'active' => 'nurse-uk.schedules.*'],
        ['label' => 'Kamar Operasi', 'icon' => 'room', 'route' => 'nurse-uk.rooms.index', 'active' => 'nurse-uk.rooms.*'],
        ['label' => 'Dokter', 'icon' => 'doctor', 'route' => 'nurse-uk.doctors.index', 'active' => 'nurse-uk.doctors.*'],
        ['label' => 'Buku Pedoman', 'icon' => 'book', 'route' => 'guidelines.index', 'active' => 'guidelines.*'],
    ];
@endphp

<nav class="space-y-1.5">
    <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-[0.22em] text-green-100/80">Menu Perawat OK</p>

    @foreach ($items as $item)
        @php
            $routeName = $item['route'] ?? null;
            $activePattern = $item['active'] ?? null;
            $isActive = $activePattern ? request()->routeIs($activePattern) : false;
            $href = $routeName && Route::has($routeName) ? route($routeName) : '#';

            $linkClass = $isActive
                ? 'bg-white/20 text-white shadow-lg backdrop-blur-md border border-white/20 scale-[1.02]'
                : 'text-green-100 hover:bg-white/10';

            $iconClass = $isActive
                ? 'bg-white/20 text-white ring-1 ring-white/20'
                : 'bg-white/10 text-green-100/90 ring-1 ring-white/10';
        @endphp

        <a
            href="{{ $href }}"
            class="flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition-all duration-300 ease-in-out {{ $linkClass }}"
        >
            <span class="flex h-10 w-10 items-center justify-center rounded-2xl {{ $iconClass }} transition-all duration-300 ease-in-out">
                @include('layouts.sidebars.icons', ['name' => $item['icon']])
            </span>
            <span class="truncate">{{ $item['label'] }}</span>
        </a>
    @endforeach
</nav>
