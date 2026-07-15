<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>MitraPOS</title>

        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
        <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&display=swap" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
        <script>
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            // Apply sidebar state immediately to avoid flash
            (function() {
                var saved = localStorage.getItem('sidebar-collapsed');
                var isCollapsed = saved !== 'false'; // default collapsed
                if (isCollapsed) {
                    document.documentElement.setAttribute('data-sidebar-collapsed', '1');
                }
            })();
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="app-shell" id="appRoot">
            @include('dashboard.partials.sidebar')
            
            <div class="sidebar-overlay" id="sidebarOverlay"></div>

            <main class="main-shell">
                @include('dashboard.partials.topbar')

                <div class="content-shell">
                    @if (session('success'))
                        <div class="flash-message success" data-auto-dismiss="5000" role="status" aria-live="polite">
                            <strong>Berhasil</strong>
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="flash-message error" data-auto-dismiss="5000" role="alert" aria-live="assertive">
                            <strong>Terjadi Kendala</strong>
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="flash-message warning" data-auto-dismiss="5000" role="alert" aria-live="assertive">
                            <strong>Validasi Gagal</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('dashboard-content')
                </div>
            </main>
        </div>

        <div class="modal" id="confirmActionModal" aria-hidden="true">
            <div class="modal-card small" role="dialog" aria-modal="true" aria-labelledby="confirmActionTitle">
                <div class="modal-head">
                    <h2 id="confirmActionTitle">Konfirmasi</h2>
                </div>
                <p class="text-sm text-slate-600" id="confirmActionMessage">Lanjutkan aksi ini?</p>
                <div class="form-actions">
                    <button class="btn btn-ghost" id="confirmActionCancel" type="button">Batal</button>
                    <button class="btn btn-primary" id="confirmActionSubmit" type="button">Ya, lanjutkan</button>
                </div>
            </div>
        </div>

        <div class="modal" id="imagePreviewModal" aria-hidden="true" style="background-color: rgba(15, 23, 42, 0.85); backdrop-filter: blur(8px);">
            <div class="relative max-w-5xl mx-auto flex flex-col items-center justify-center p-4" id="imagePreviewModalCard">
                <button type="button" id="closeImagePreviewModal" class="absolute -top-12 right-4 text-white hover:text-slate-300 focus:outline-none" style="font-size: 2.5rem; line-height: 1;">
                    &times;
                </button>
                <div class="overflow-hidden rounded-2xl bg-white/5 p-2 shadow-2xl ring-1 ring-white/10">
                    <img id="imagePreviewModalTarget" src="" alt="Preview Foto" class="max-h-[75vh] max-w-full rounded-xl object-contain">
                </div>
            </div>
        </div>

        @yield('dashboard-modals')

        <style>
            @media (max-width: 1023px) {
                #appRoot, #appRoot .main-shell {
                    display: flex !important;
                    flex-direction: column !important;
                    width: 100vw !important;
                    max-width: 100vw !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    left: 0 !important;
                    position: relative !important;
                    overflow-x: hidden !important;
                    grid-template-columns: 1fr !important;
                }
                #appRoot .pemilik-topbar {
                    width: 100vw !important;
                    max-width: 100vw !important;
                    position: relative !important;
                    overflow: visible !important;
                }
                #appRoot .content-shell {
                    display: block !important;
                    width: 100vw !important;
                    max-width: 100vw !important;
                    margin: 0 !important;
                    padding: 12px !important;
                    overflow-x: hidden !important;
                }
                #appRoot .section-head {
                    display: block !important;
                    width: 100% !important;
                    min-width: 0 !important;
                    margin-bottom: 20px !important;
                    text-align: left !important;
                }
                #appRoot .section-head > div {
                    display: block !important;
                    width: 100% !important;
                    margin-bottom: 12px !important;
                }
                #appRoot .section-head h1, #appRoot .section-head p {
                    width: 100% !important;
                    max-width: 100% !important;
                    white-space: normal !important;
                    overflow-wrap: break-word !important;
                    word-break: break-word !important;
                    display: block !important;
                    margin: 0 !important;
                }
                #appRoot .section-head .btn {
                    display: inline-block !important;
                    width: auto !important;
                    margin-top: 10px !important;
                }
                #appRoot .toolbar {
                    display: flex !important;
                    flex-direction: column !important;
                    gap: 12px !important;
                    width: 100% !important;
                    margin-bottom: 15px !important;
                }
                #appRoot .field {
                    width: 100% !important;
                    min-width: 0 !important;
                }
                #appRoot .table-info {
                    text-align: left !important;
                    display: block !important;
                    width: 100% !important;
                }
                #appRoot .table-wrap {
                    width: calc(100vw - 24px) !important;
                    max-width: calc(100vw - 24px) !important;
                    overflow-x: auto !important;
                    display: block !important;
                    border-radius: 8px !important;
                }
                #appRoot #sidebar {
                    display: none !important;
                }
                #appRoot #sidebar.mobile-open {
                    display: block !important;
                }
            }
        </style>
        <script>
            function checkResponsive() {
                if (window.innerWidth < 1024) {
                    document.body.style.overflowX = 'hidden';
                    document.documentElement.style.overflowX = 'hidden';
                }
            }
            window.addEventListener('load', checkResponsive);
            window.addEventListener('resize', checkResponsive);
            checkResponsive();
        </script>
    </body>
</html>
