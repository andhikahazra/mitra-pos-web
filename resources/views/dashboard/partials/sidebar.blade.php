<aside class="pemilik-sidebar" id="sidebar">
    <div class="sidebar-head">
        <button class="brand-icon brand-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">M</button>
        <div class="brand-text">
            <p class="brand-name">MitraPOS</p>
            <p class="brand-role">MitraPOS pemilik</p>
        </div>
    </div>

    <div class="sidebar-search-wrap">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        <input id="globalSearch" type="text" placeholder="Quick search...">
        <span>Ctrl K</span>
    </div>

    <nav class="sidebar-menu" id="mainMenu">
        <p class="menu-group-title">Monitoring</p>
        <a class="menu-link {{ request()->routeIs('pemilik.dashboard') ? 'active' : '' }}" href="{{ route('pemilik.dashboard') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 10.5L12 3l9 7.5V20a1 1 0 0 1-1 1h-5.5v-6h-5v6H4a1 1 0 0 1-1-1v-9.5Z"/></svg>
            <span>Dashboard</span>
        </a>

        <a class="menu-link {{ request()->routeIs('rop.index') ? 'active' : '' }}" href="{{ route('rop.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 3v18h18"/><path d="m7 14 3-3 3 2 4-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span>Report ROP</span>
        </a>

        <a class="menu-link {{ request()->routeIs('produk.*') ? 'active' : '' }}" href="{{ route('produk.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m3 7 9-4 9 4-9 4-9-4Z"/><path d="m3 7 9 4 9-4"/><path d="M3 7v10l9 4 9-4V7"/></svg>
            <span>Manajemen Produk</span>
        </a>

        <a class="menu-link {{ request()->routeIs('supplier.*') ? 'active' : '' }}" href="{{ route('supplier.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <span>Manajemen Supplier</span>
        </a>

        <p class="menu-group-title">Operasional</p>
        <a class="menu-link {{ request()->routeIs('barang-masuk.*') ? 'active' : '' }}" href="{{ route('barang-masuk.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 7h12v10H3z"/><path d="M15 10h3l3 3v4h-6Z"/><circle cx="8" cy="18" r="1.8"/><circle cx="18" cy="18" r="1.8"/></svg>
            <span>Barang Masuk</span>
        </a>

        <a class="menu-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="8" r="3"/><path d="M3 19c0-2.6 2.7-4.5 6-4.5"/><circle cx="17" cy="10" r="2.5"/><path d="M13.5 19c.5-2 2.2-3.5 4.5-3.5 1.7 0 3.2.8 4 2"/></svg>
            <span>Manajemen User</span>
        </a>

        <a class="menu-link {{ request()->routeIs('transaksi.*') ? 'active' : '' }}" href="{{ route('transaksi.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 6h16v14H4z"/><path d="M4 10h16"/><path d="M8 16h3"/></svg>
            <span>Riwayat Transaksi</span>
        </a>

        <a class="menu-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}" href="{{ route('laporan.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 10h18"/><path d="M7 15h.01"/><path d="M11 15h.01"/><path d="M15 15h.01"/><path d="M7 18h.01"/><path d="M11 18h.01"/><path d="M15 18h.01"/></svg>
            <span>Laporan Keuangan</span>
        </a>

        <p class="menu-group-title">Inventori</p>
        <a class="menu-link {{ request()->routeIs('log-stok.*') ? 'active' : '' }}" href="{{ route('log-stok.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 20V10M18 20V4M6 20v-4"/></svg>
            <span>Log Pergerakan Stok</span>
        </a>

        <a class="menu-link {{ request()->routeIs('stok-batch.*') ? 'active' : '' }}" href="{{ route('stok-batch.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5M12 22V12"/></svg>
            <span>Monitoring Batch</span>
        </a>

        <p class="menu-group-title">System</p>
        <a class="menu-link {{ request()->routeIs('settings.index') ? 'active' : '' }}" href="{{ route('settings.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z"/><path d="M19.4 15a1 1 0 0 0 .2 1.1l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1 1 0 0 0-1.1-.2 1 1 0 0 0-.6.9V20a2 2 0 1 1-4 0v-.2a1 1 0 0 0-.6-.9 1 1 0 0 0-1.1.2l-.1.1a2 2 0 0 1-2.8-2.8l.1-.1a1 1 0 0 0 .2-1.1 1 1 0 0 0-.9-.6H4a2 2 0 1 1 0-4h.2a1 1 0 0 0 .9-.6 1 1 0 0 0-.2-1.1L4.8 8.6a2 2 0 1 1 2.8-2.8l.1.1a1 1 0 0 0 1.1.2 1 1 0 0 0 .6-.9V5a2 2 0 1 1 4 0v.2a1 1 0 0 0 .6.9 1 1 0 0 0 1.1-.2l.1-.1a2 2 0 0 1 2.8 2.8l-.1.1a1 1 0 0 0-.2 1.1 1 1 0 0 0 .9.6h.2a2 2 0 1 1 0 4h-.2a1 1 0 0 0-.9.6Z"/></svg>
            <span>Pengaturan</span>
        </a>
    </nav>
</aside>
