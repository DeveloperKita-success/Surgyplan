@php
    $items = [
        [
            'label' => 'Dashboard',
            'icon' => 'dashboard',
            'route' => 'nurse-regular.dashboard',
            'active' => 'nurse-regular.dashboard',
        ],
        ['label' => 'Data Pasien', 'icon' => 'user', 'route' => 'nurse-regular.patients.index', 'active' => 'nurse-regular.patients.*'],
        [
            'label' => 'Daftar Pengajuan',
            'icon' => 'records',
            'route' => 'nurse-regular.surgery-requests.index',
            'active' => 'nurse-regular.surgery-requests.*',
        ],
    ];
@endphp

<nav class="space-y-1.5">
    <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-[0.22em] text-green-100/80">Menu Perawat</p>

    @foreach ($items as $item)
        @include('layouts.sidebars.menu-item', ['item' => $item])
    @endforeach
</nav>
