@php
    $items = [
        ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'nurse-ok.dashboard', 'active' => 'nurse-ok.dashboard'],
        ['label' => 'Data Pasien', 'icon' => 'user', 'route' => 'nurse-ok.patients.index', 'active' => 'nurse-ok.patients.*'],
        ['label' => 'Pengajuan Operasi', 'icon' => 'clipboard', 'route' => 'nurse-ok.requests.index', 'active' => 'nurse-ok.requests.*'],
        ['label' => 'Jadwal Operasi', 'icon' => 'calendar', 'route' => 'nurse-ok.schedules.index', 'active' => 'nurse-ok.schedules.*'],
        ['label' => 'Kamar Operasi', 'icon' => 'room', 'route' => 'nurse-ok.rooms.index', 'active' => 'nurse-ok.rooms.*'],
        ['label' => 'Dokter', 'icon' => 'doctor', 'route' => 'nurse-ok.doctors.index', 'active' => 'nurse-ok.doctors.*'],
    ];
@endphp

<nav class="space-y-1.5">
    <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-[0.22em] text-green-100/80">Menu Perawat OK</p>

    @foreach ($items as $item)
        @include('layouts.sidebars.menu-item', ['item' => $item])
    @endforeach
</nav>
