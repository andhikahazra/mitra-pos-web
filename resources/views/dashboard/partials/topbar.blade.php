<header class="pemilik-topbar">
    <div class="topbar-shell">
        <div class="flex items-center gap-3">
            <button class="icon-control lg:hidden" id="mobileMenuBtn" aria-label="Open Menu">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="topbar-meta">
                <p class="topbar-kicker">pemilik Workspace</p>
                <h2 class="topbar-title">MitraPOS Control Center</h2>
            </div>
        </div>

        <div class="utility-links">

            <button class="icon-control" id="themeToggle" aria-label="Toggle dark mode">
                <svg id="themeToggleDarkIcon" class="hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                <svg id="themeToggleLightIcon" class="hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            </button>

            <button class="profile-pill" id="profileBtn">
                <span class="profile-initial">OM</span>
                <span class="profile-label">{{ auth()->user()->nama ?? 'pemilik MitraPOS' }}</span>
            </button>
            <a href="{{ route('logout') }}" class="utility-link">Logout</a>
        </div>
    </div>
</header>
