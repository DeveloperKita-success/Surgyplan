<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150 dark:bg-emerald-500 dark:hover:bg-emerald-400 dark:focus:bg-emerald-400 dark:active:bg-emerald-300 dark:focus:ring-offset-slate-900']) }}>
    {{ $slot }}
</button>
