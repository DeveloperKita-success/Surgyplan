@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full border-l-4 border-green-500 bg-green-50/80 py-2 pe-4 ps-3 text-start text-base font-semibold text-emerald-700 focus:outline-none focus:bg-green-50 focus:text-emerald-800 focus:border-green-600 transition duration-150 ease-in-out'
            : 'block w-full border-l-4 border-transparent py-2 pe-4 ps-3 text-start text-base font-semibold text-slate-700 hover:bg-green-50 hover:text-emerald-700 hover:border-green-200 focus:outline-none focus:bg-green-50 focus:text-emerald-700 focus:border-green-200 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
