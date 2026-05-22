@php
    $items = [
        ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'doctor.dashboard', 'active' => 'doctor.dashboard'],
        ['label' => 'Jadwal Operasi', 'icon' => 'calendar', 'route' => 'doctor.schedules.index', 'active' => 'doctor.schedules.*'],
    ];
@endphp

<nav class="space-y-1.5">
    <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-[0.22em] text-green-100/80">Menu Dokter</p>

    @foreach ($items as $item)
        @include('layouts.sidebars.menu-item', ['item' => $item])
    @endforeach
</nav>
