<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>

    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:url" content="{{ $ogUrl }}">
    <meta property="og:site_name" content="{{ $setting->nama_toko ?? 'MitraPOS' }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Geist', 'system-ui', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                },
            },
        };
    </script>
    <style>
        @media print {
            body { background: white; }
            .no-print { display: none; }
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-b from-slate-50 to-white font-sans">
    <main class="max-w-md mx-auto p-4 md:p-8">
        <article class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            {{-- Store Header --}}
            <header class="bg-slate-900 text-white p-6 text-center">
                <h1 class="text-xl font-bold tracking-tight">{{ $setting->nama_toko ?? 'MitraPOS' }}</h1>
                @if($alamat)
                    <p class="mt-1 text-sm text-slate-300">{{ $alamat }}</p>
                @endif
                @if($setting->no_hp)
                    <p class="mt-0.5 text-sm text-slate-300">{{ $setting->no_hp }}</p>
                @endif
            </header>

            <div class="p-6">
                {{-- Info Baris --}}
                <div class="border-b border-slate-200 pb-4 mb-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">No. Transaksi</span>
                        <span class="font-medium font-mono text-slate-800">{{ $transaksi->kode }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Tanggal</span>
                        <span class="font-medium text-slate-800">{{ $transaksi->tanggal?->format('d M Y H:i') ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Kasir</span>
                        <span class="font-medium text-slate-800">{{ $transaksi->user->nama ?? '-' }}</span>
                    </div>
                    @if($transaksi->nama_pelanggan)
                        <div class="flex justify-between">
                            <span class="text-slate-500">Pelanggan</span>
                            <span class="font-medium text-slate-800">{{ $transaksi->nama_pelanggan }}</span>
                        </div>
                    @endif
                    @if($transaksi->catatan)
                        <div class="flex justify-between">
                            <span class="text-slate-500">Catatan</span>
                            <span class="font-medium text-slate-800">{{ $transaksi->catatan }}</span>
                        </div>
                    @endif
                </div>

                {{-- Tabel Item --}}
                <table class="w-full text-sm mb-4">
                    <thead class="text-xs uppercase tracking-wider text-slate-400 border-b border-slate-200">
                        <tr>
                            <th class="text-left pb-2 font-medium w-8">No</th>
                            <th class="text-left pb-2 font-medium">Item</th>
                            <th class="text-right pb-2 font-medium w-16">Qty</th>
                            <th class="text-right pb-2 font-medium w-32">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($items as $i => $item)
                            <tr class="py-0.5">
                                <td class="text-slate-400 text-xs py-2 align-top">{{ $i + 1 }}</td>
                                <td class="py-2">
                                    <span class="font-medium text-slate-800">{{ $item['name'] }}</span>
                                    <span class="block text-xs text-slate-400">Rp {{ number_format($item['price'], 0, ',', '.') }} x {{ $item['qty'] }}</span>
                                </td>
                                <td class="text-right py-2 align-top text-slate-600">{{ $item['qty'] }}</td>
                                <td class="text-right py-2 align-top font-medium text-slate-800">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-slate-400 py-6">Belum ada item.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Total --}}
                <div class="border-t border-slate-200 pt-4 space-y-1.5 text-sm">
                    <div class="flex justify-between text-slate-500">
                        <span>Subtotal</span>
                        <span class="font-medium text-slate-700">Rp {{ number_format($totals['subtotal'], 0, ',', '.') }}</span>
                    </div>
                    @if($totals['admin'] > 0)
                        <div class="flex justify-between text-slate-500">
                            <span>Biaya Admin ({{ $totals['method'] }})</span>
                            <span class="font-medium text-slate-700">Rp {{ number_format($totals['admin'], 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-base font-bold border-t border-slate-200 pt-3 mt-3">
                        <span class="text-slate-800">TOTAL</span>
                        <span class="text-emerald-600">Rp {{ number_format($totals['grand_total'], 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Metode Bayar --}}
                <div class="mt-5 p-3 bg-slate-50 rounded-xl text-sm flex justify-between items-center">
                    <span class="font-medium text-slate-500">Pembayaran</span>
                    <span class="font-semibold text-slate-800">{{ $totals['method'] }}</span>
                </div>

                @if($setting->footer_nota)
                    <div class="mt-6 pt-4 border-t border-slate-100 text-center text-xs text-slate-400">
                        {{ $setting->footer_nota }}
                    </div>
                @endif

                <div class="mt-4 text-center text-xs text-slate-400">
                    Terima kasih telah berbelanja di {{ $setting->nama_toko ?? 'MitraPOS' }}!
                </div>
            </div>
        </article>

        <p class="mt-4 text-center no-print">
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-slate-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Nota
            </button>
        </p>
    </main>
</body>
</html>