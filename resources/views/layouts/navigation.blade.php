<nav
    x-data="{
        open: false,
        dayDate: '',
        time: '',
        init() {
            this.tick();
            setInterval(() => this.tick(), 1000);
        },
        tick() {
            const now = new Date();
            this.dayDate = new Intl.DateTimeFormat('id-ID', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            }).format(now);
            this.time = new Intl.DateTimeFormat('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            }).format(now);
        }
    }"
    class="fixed inset-x-0 top-0 z-50 border-b border-slate-100/80 bg-[#fbfcfb]/92 shadow-[0_8px_30px_rgba(15,23,42,0.04)] backdrop-blur-xl"
>
    <div class="flex h-16 items-center sm:h-[72px]">
        <div class="flex h-full min-w-0 flex-1 items-center gap-2 px-3 sm:w-72 sm:flex-none sm:gap-3 sm:px-5">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden sm:h-12 sm:w-12">
                <img src="{{ asset('image/splash.png') }}" alt="Logo SurgeryPlan" class="h-10 w-10 object-contain sm:h-12 sm:w-12">
            </div>
            <div class="min-w-0">
                <p class="truncate text-sm font-bold tracking-normal text-teal-950 sm:text-lg">SurgeryPlan</p>
                <p class="hidden truncate text-xs font-semibold text-slate-500 sm:block">Hospital Dashboard</p>
            </div>
        </div>

        <div class="flex h-full shrink-0 items-center gap-2 px-2 sm:min-w-0 sm:flex-1 sm:justify-between sm:gap-3 sm:px-7 lg:px-10">
            <button
                type="button"
                @click="$dispatch('sidebar-open')"
                class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-slate-600 transition hover:bg-slate-100 hover:text-teal-800 focus:outline-none focus:ring-2 focus:ring-teal-700/20 lg:hidden"
                aria-label="Buka sidebar"
            >
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <div class="flex min-w-0 items-center gap-1.5 sm:gap-3">
                <div class="hidden items-center gap-2 rounded-xl border border-slate-100 bg-white/90 px-3 py-2 text-sm font-semibold text-slate-700 shadow-[0_8px_24px_rgba(15,23,42,0.04)] sm:flex">
                    <svg class="h-4 w-4 shrink-0 text-teal-800" viewBox="0 0 24 24" fill="none">
                        <path d="M8 2v3M16 2v3M4 10h16M6 5h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span x-text="dayDate">{{ now()->translatedFormat('l, d F Y') }}</span>
                </div>

                <div class="flex items-center gap-1.5 rounded-xl border border-slate-100 bg-white/90 px-2.5 py-2 text-xs font-semibold text-slate-700 shadow-[0_8px_24px_rgba(15,23,42,0.04)] sm:gap-2 sm:px-3 sm:text-sm">
                    <svg class="h-4 w-4 shrink-0 text-teal-800" viewBox="0 0 24 24" fill="none">
                        <path d="M12 6v6l4 2M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span x-text="time">{{ now()->format('H:i') }}</span>
                </div>
            </div>

            <div class="flex shrink-0 items-center sm:ml-auto">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            type="button"
                            class="flex items-center gap-2 rounded-xl border border-slate-100 bg-white/90 px-1.5 py-1.5 text-left shadow-[0_8px_24px_rgba(15,23,42,0.04)] transition hover:bg-white focus:outline-none focus:ring-2 focus:ring-teal-700/20 sm:gap-3 sm:px-3"
                        >
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-teal-900 text-xs font-bold text-white sm:h-9 sm:w-9 sm:text-sm">
                                {{
                                    collect(explode(' ', $user?->name ?? Auth::user()?->name ?? 'U'))
                                        ->map(fn ($part) => mb_substr($part, 0, 1))
                                        ->take(2)
                                        ->implode('')
                                }}
                            </span>
                            <span class="hidden min-w-0 md:block">
                                <span class="block max-w-40 truncate text-sm font-bold text-slate-900">{{ $user?->name ?? Auth::user()?->name }}</span>
                                <span class="block text-xs font-medium text-slate-500">{{ $roleLabel ?? 'Pengguna' }}</span>
                            </span>
                            <span class="hidden text-slate-500 sm:block">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Pengaturan
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Logout
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>
</nav>
