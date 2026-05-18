@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center rounded-xl px-3 py-2 border-b-2 border-green-500 text-sm font-semibold leading-5 text-emerald-700 focus:outline-none focus:border-green-600 transition duration-150 ease-in-out'
            : 'inline-flex items-center rounded-xl px-3 py-2 border-b-2 border-transparent text-sm font-semibold leading-5 text-slate-600 hover:text-emerald-700 hover:bg-green-50 hover:border-green-200 focus:outline-none focus:text-emerald-700 focus:border-green-200 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
