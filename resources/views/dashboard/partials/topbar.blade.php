<header class="pemilik-topbar">
    <div class="topbar-shell">
        <div class="flex items-center gap-3">
            <button class="icon-control lg:hidden" id="mobileMenuBtn" aria-label="Open Menu">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="topbar-meta">
                <p class="topbar-kicker">MitraPOS</p>
                <h2 class="topbar-title">{{ request()->is('/') ? 'Dashboard' : Str::replaceFirst('pemilik.', '', Route::currentRouteName()) }}</h2>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button class="icon-control" id="themeToggle" aria-label="Toggle dark mode">
                <svg id="themeToggleDarkIcon" class="hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                <svg id="themeToggleLightIcon" class="hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            </button>

            <div class="h-5 w-px bg-slate-200 dark:bg-zinc-700"></div>

            <div class="relative" id="profileDropdownWrap">
                <button class="flex items-center gap-2 rounded-lg p-1 transition-colors hover:bg-slate-100 dark:hover:bg-zinc-800" id="profileDropdownBtn" aria-haspopup="true" aria-expanded="false">
                    <span class="w-7 h-7 rounded-full bg-slate-100 dark:bg-zinc-800 flex items-center justify-center text-xs font-medium text-slate-600 dark:text-zinc-400">
                        @php
                            $name = auth()->user()->nama ?? 'Owner';
                            $words = explode(' ', $name);
                            $initials = '';
                            foreach ($words as $word) {
                                $initials .= strtoupper(substr($word, 0, 1));
                            }
                            echo substr($initials, 0, 2);
                        @endphp
                    </span>
                    <svg class="w-4 h-4 text-slate-400 dark:text-zinc-500 transition-transform" id="profileChevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                </button>

                <div class="hidden absolute right-0 top-full mt-2 w-56 rounded-xl border border-slate-200 bg-white shadow-lg dark:border-zinc-700 dark:bg-zinc-900 z-50" id="profileDropdownMenu">
                    <div class="px-4 py-3 border-b border-slate-100 dark:border-zinc-800">
                        <p class="text-sm font-medium text-slate-800 dark:text-zinc-200">{{ auth()->user()->nama ?? 'Owner' }}</p>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 mt-0.5">{{ auth()->user()->email }}</p>
                    </div>
                    <div class="py-1">
                        <a href="{{ route('settings.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 dark:text-zinc-300 dark:hover:bg-zinc-800">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                            Pengaturan
                        </a>
                        <a href="{{ route('logout') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
