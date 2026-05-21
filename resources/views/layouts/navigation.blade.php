<nav
    x-data="{ open: false }"
    class="fixed inset-x-0 top-0 z-30 border-b border-green-100 bg-white/70 shadow-sm backdrop-blur-xl lg:pl-64"
>
    <!-- Primary Navigation Menu -->
    <div class="px-6 sm:px-8">
        <div class="flex h-16 items-center justify-between">
            <div class="flex">
                <!-- Sidebar Toggle (Mobile) -->
                <div class="-ms-2 me-2 flex items-center lg:hidden">
                    <button
                        type="button"
                        @click="$dispatch('sidebar-open')"
                        class="inline-flex items-center justify-center rounded-xl p-2 text-slate-600 hover:bg-green-50 hover:text-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600/30"
                        aria-label="Buka sidebar"
                    >
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto" />
                    </a>
                </div>
            </div>

            <!-- Date & Time -->
            <div class="hidden items-center gap-3 rounded-xl border border-green-100 bg-white/70 px-4 py-2 text-sm text-slate-700 shadow-sm ring-1 ring-white/40 backdrop-blur sm:flex">
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-emerald-600" viewBox="0 0 24 24" fill="none">
                        <path d="M8 2v3M16 2v3M4 10h16M6 5h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="font-semibold">{{ now()->translatedFormat('l, d F Y') }}</span>
                </div>
                <span class="h-5 w-px bg-slate-200"></span>
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-emerald-600" viewBox="0 0 24 24" fill="none">
                        <path d="M12 6v6l4 2M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="font-semibold">{{ now()->format('H:i') }}</span>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-4">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            type="button"
                            class="flex items-center gap-3 rounded-xl border border-green-200 bg-white/60 px-3 py-2 text-left shadow-sm ring-1 ring-white/40 backdrop-blur hover:bg-white/80 focus:outline-none focus:ring-2 focus:ring-emerald-600/30"
                        >
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-sm font-bold text-emerald-800">
                                {{
                                    collect(explode(' ', $user?->name ?? Auth::user()?->name ?? 'U'))
                                        ->map(fn ($part) => mb_substr($part, 0, 1))
                                        ->take(2)
                                        ->implode('')
                                }}
                            </span>
                            <span class="hidden sm:block">
                                <span class="block max-w-40 truncate text-sm font-semibold text-slate-900">{{ $user?->name ?? Auth::user()?->name }}</span>
                                <span class="block text-xs text-slate-500">{{ $roleLabel ?? 'Pengguna' }}</span>
                            </span>
                            <span class="ms-1 text-slate-500">
                                <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-xl p-2 text-slate-600 hover:bg-green-50 hover:text-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600/30 transition duration-150 ease-in-out" aria-label="Buka menu">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <!-- Responsive Settings Options -->
        <div class="border-t border-green-100 pb-1 pt-4">
            <div class="px-4">
                <div class="text-base font-semibold text-slate-900">{{ Auth::user()->name }}</div>
                <div class="text-sm font-medium text-slate-600">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
