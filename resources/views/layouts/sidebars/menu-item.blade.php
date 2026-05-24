@php
    $routeName = $item['route'] ?? null;
    $activePattern = $item['active'] ?? null;
    $isActive = $activePattern ? request()->routeIs($activePattern) : false;
    $href = $routeName && Route::has($routeName) ? route($routeName) : '#';

    $linkClass = $isActive
        ? 'bg-white/[0.16] text-white shadow-[inset_0_1px_0_rgba(255,255,255,0.12),0_10px_26px_rgba(0,0,0,0.10)] backdrop-blur-md border border-white/15 scale-[1.01]'
        : 'text-emerald-50/90 hover:bg-white/[0.08] hover:text-white';

    $iconClass = $isActive
        ? 'bg-white/18 text-white ring-1 ring-white/15'
        : 'bg-white/[0.08] text-emerald-100/90 ring-1 ring-white/10';
@endphp

<a
    href="{{ $href }}"
    class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm font-semibold transition-all duration-300 ease-in-out {{ $linkClass }}"
>
    <span class="flex h-10 w-10 items-center justify-center rounded-2xl {{ $iconClass }} transition-all duration-300 ease-in-out">
        @include('layouts.sidebars.icons', ['name' => $item['icon']])
    </span>
    <span class="truncate">{{ $item['label'] }}</span>
</a>
