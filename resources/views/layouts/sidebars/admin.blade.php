@php
    $items = [
        ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard'],
        ['label' => 'Manajemen User', 'icon' => 'user', 'route' => 'admin.users.index', 'active' => 'admin.users.*'],
        ['label' => 'Data Pendukung', 'icon' => 'clipboard', 'route' => 'admin.specialists.index', 'active' => 'admin.specialists.*'],
    ];
@endphp

<nav class="space-y-2">
    <p class="px-3 pb-3 text-xs font-semibold uppercase tracking-[0.22em] text-emerald-100/75">MENU ADMIN</p>

    @foreach ($items as $item)
        @include('layouts.sidebars.menu-item', ['item' => $item])
    @endforeach
</nav>
