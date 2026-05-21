@php
    $items = [
        ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'nurse-uk.dashboard', 'active' => 'nurse-uk.dashboard'],
        ['label' => 'Data Pasien', 'icon' => 'user', 'route' => 'nurse-uk.patients.index', 'active' => 'nurse-uk.patients.*'],
        ['label' => 'Pengajuan Operasi', 'icon' => 'clipboard', 'route' => 'nurse-uk.requests.index', 'active' => 'nurse-uk.requests.*'],
        ['label' => 'Jadwal Operasi', 'icon' => 'calendar', 'route' => 'nurse-uk.schedules.index', 'active' => 'nurse-uk.schedules.*'],
        ['label' => 'Kamar Operasi', 'icon' => 'room', 'route' => 'nurse-uk.rooms.index', 'active' => 'nurse-uk.rooms.*'],
        ['label' => 'Dokter', 'icon' => 'doctor', 'route' => 'nurse-uk.doctors.index', 'active' => 'nurse-uk.doctors.*'],
    ];
@endphp

<nav class="space-y-1.5">
    <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-[0.22em] text-green-100/80">Menu Perawat OK</p>

    @foreach ($items as $item)
        @include('layouts.sidebars.menu-item', ['item' => $item])
    @endforeach
</nav>
