@php
    $items = [
        ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'doctor.dashboard', 'active' => 'doctor.dashboard'],
        ['label' => 'Jadwal Operasi', 'icon' => 'calendar', 'route' => 'doctor.schedules.index', 'active' => 'doctor.schedules.*'],
        // ['label' => 'Riwayat Operasi', 'icon' => 'report', 'route' => 'doctor.schedules.index', 'active' => 'doctor.reports.*'],
    ];
@endphp

<nav class="space-y-2">
    <p class="px-3 pb-3 text-xs font-semibold uppercase tracking-[0.22em] text-emerald-100/75">MENU DOKTER</p>

    @foreach ($items as $item)
        @include('layouts.sidebars.menu-item', ['item' => $item])
    @endforeach
</nav>
