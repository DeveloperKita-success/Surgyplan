@props(['disabled' => false])

<input
	@disabled($disabled)
	{{ $attributes->merge([
		'class' => 'rounded-md border-slate-300 bg-white text-slate-900 shadow-sm placeholder-slate-400 focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:placeholder-slate-400 dark:focus:border-emerald-400 dark:focus:ring-emerald-400',
	]) }}
>
