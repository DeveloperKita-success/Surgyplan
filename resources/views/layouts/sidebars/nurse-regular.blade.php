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

<nav class="space-y-2">
    <p class="px-3 pb-3 text-xs font-semibold uppercase tracking-[0.22em] text-emerald-100/75">MENU PERAWAT</p>

    @foreach ($items as $item)
        @include('layouts.sidebars.menu-item', ['item' => $item])
    @endforeach
</nav>
