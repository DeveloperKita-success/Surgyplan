<nav class="space-y-2">
    <p class="px-3 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Menu Perawat Reguler</p>

    <a href="{{ route('dashboard.nurse.regular') }}" class="flex items-center gap-3 rounded-lg bg-cyan-50 px-3 py-3 text-sm font-semibold text-cyan-800">
        <span class="flex h-9 w-9 items-center justify-center rounded-md bg-white text-cyan-700 shadow-sm">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M4 13h6V4H4v9Zm10 7h6V4h-6v16ZM4 20h6v-3H4v3Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
        </span>
        Dashboard
    </a>

    @foreach ([
        ['label' => 'Data Pasien', 'icon' => 'user'],
        ['label' => 'Pengajuan Operasi', 'icon' => 'clipboard'],
        ['label' => 'Buku Pedoman', 'icon' => 'book'],
    ] as $item)
        <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">
            <span class="flex h-9 w-9 items-center justify-center rounded-md bg-slate-100 text-slate-500">
                @include('layouts.sidebars.icons', ['name' => $item['icon']])
            </span>
            {{ $item['label'] }}
        </a>
    @endforeach
</nav>
