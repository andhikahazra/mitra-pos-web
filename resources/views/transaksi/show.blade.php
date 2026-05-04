@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
<section class="feature-section" style="display:block;opacity:1;visibility:visible;" id="section-transaksi-show">
    <div class="section-head">
        <div>
            <h1>Detail Transaksi</h1>
            <p>Informasi lengkap transaksi POS untuk audit pemilik.</p>
        </div>
        <a href="{{ route('transaksi.index') }}" class="btn btn-ghost">Kembali ke Riwayat</a>
    </div>

    <article class="panel-card">
        {{-- Header --}}
        <div class="mb-4 flex flex-wrap items-center justify-between gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800/60 dark:bg-[#fdfdfd]/[0.02]">
            <div class="flex items-center gap-4">
                <div class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-['Poppins'] text-lg font-bold tracking-tight text-slate-800 dark:text-slate-100 sm:text-xl">
                        {{ $transaksi->kode }}
                    </h2>
                    @if($transaksi->status === 'Selesai')
                        <span class="mt-1 inline-flex items-center gap-1.5 rounded-md bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold tracking-wide text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                            <span class="grid h-1.5 w-1.5 place-items-center rounded-full bg-emerald-500"></span> Selesai
                        </span>
                    @else
                        <span class="mt-1 inline-flex items-center gap-1.5 rounded-md bg-amber-50 px-2 py-0.5 text-[10px] font-semibold tracking-wide text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">
                            <span class="grid h-1.5 w-1.5 place-items-center rounded-full bg-amber-500"></span> Tertunda
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="trx-detail-layout">
            <article class="trx-summary-card">
                <p class="trx-summary-label">Staff</p>
                <h3>{{ $transaksi->user->nama ?? '-' }}</h3>
                <p class="trx-summary-sub">Bertanggung jawab atas transaksi</p>
            </article>

            <article class="trx-summary-card">
                <p class="trx-summary-label">Waktu Rekam</p>
                <h3>{{ $transaksi->tanggal ? $transaksi->tanggal->format('d M Y') : '-' }}</h3>
                <p class="trx-summary-sub">Tercatat pada sistem inventori</p>
            </article>

            <article class="trx-summary-card total">
                <p class="trx-summary-label">Total Nominal</p>
                <h3>Rp {{ number_format((float) $transaksi->total_harga, 0, ',', '.') }}</h3>
                <p class="trx-summary-sub">Akumulasi {{ $items->count() }} item keluar</p>
            </article>
        </div>

        {{-- Items Table --}}
        <article class="trx-items-card mt-6">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4 dark:border-slate-800/80">
                <div>
                    <h2 class="text-base font-semibold text-slate-800 dark:text-slate-200">List Barang Keluar</h2>
                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Rincian item untuk transaksi ini</p>
                </div>
                <span class="inline-flex items-center gap-1.5 rounded bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                    {{ $items->count() }} Baris
                </span>
            </div>

            <div class="max-w-full overflow-x-auto pb-2">
                <table class="w-full min-w-[600px] border-collapse text-left text-sm text-slate-600 dark:text-slate-400">
                    <thead class="bg-slate-50/80 text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:bg-slate-800/50">
                        <tr>
                            <th class="w-12 rounded-l-lg border-b border-slate-100 py-3 text-center dark:border-slate-800/80">No</th>
                            <th class="border-b border-slate-100 py-3 dark:border-slate-800/80">Nama Barang</th>
                            <th class="border-b border-slate-100 py-3 text-right dark:border-slate-800/80 w-32">Kuantitas</th>
                            <th class="border-b border-slate-100 py-3 text-right dark:border-slate-800/80 w-40">Harga Satuan</th>
                            <th class="rounded-r-lg border-b border-slate-100 py-3 text-right dark:border-slate-800/80 w-40">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="align-top">
                        @forelse($items as $i => $item)
                            <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/30">
                                <td class="font-medium text-slate-500 w-12 text-center border-b border-slate-100 dark:border-slate-800/80">{{ $i + 1 }}</td>
                                <td class="border-b border-slate-100 dark:border-slate-800/80 py-4">
                                    <span class="block font-medium text-slate-700 dark:text-slate-200">{{ $item['name'] }}</span>
                                </td>
                                <td class="text-right border-b border-slate-100 dark:border-slate-800/80 w-32 py-4">
                                    <div class="inline-flex shrink-0 items-center justify-center gap-1 rounded bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                        {{ $item['qty'] }} <span class="font-normal opacity-75">{{ $item['unit'] }}</span>
                                    </div>
                                </td>
                                <td class="text-right whitespace-nowrap text-slate-600 dark:text-slate-400 border-b border-slate-100 dark:border-slate-800/80 w-40 py-4">
                                    Rp {{ number_format($item['price'], 0, ',', '.') }}
                                </td>
                                <td class="text-right whitespace-nowrap font-medium text-slate-700 dark:text-slate-300 border-b border-slate-100 dark:border-slate-800/80 w-40 py-4">
                                    Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-slate-500 py-6">Belum ada rincian barang.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="border-t-2 border-slate-100 text-sm font-semibold dark:border-slate-800/80">
                        <tr>
                            <td colspan="4" class="py-5 text-right font-medium text-slate-600 dark:text-slate-400">Total Akhir</td>
                            <td class="py-5 text-right text-lg font-bold text-slate-800 dark:text-slate-100">
                                Rp {{ number_format((float) $transaksi->total_harga, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </article>
    </article>
</section>
@endsection
