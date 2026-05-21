@php
    $items = [
        ['label' => 'Dashboard', 'icon' => 'shield', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard'],
        ['label' => 'Manajemen User', 'icon' => 'user', 'route' => 'admin.users.index', 'active' => 'admin.users.*'],
        ['label' => 'Data Pendukung', 'icon' => 'clipboard', 'route' => 'admin.specialists.index', 'active' => 'admin.specialists.*'],
    ];
@endphp

<nav class="space-y-1.5">
    <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-[0.22em] text-green-100/80">Menu Admin</p>

    @foreach ($items as $item)
        @include('layouts.sidebars.menu-item', ['item' => $item])
    @endforeach
</nav>
