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