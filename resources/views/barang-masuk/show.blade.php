@extends('dashboard.layouts.pemilik')

@section('dashboard-content')
<section class="feature-section active" style="display:block;opacity:1;visibility:visible;" id="section-incoming-review">
    <div class="section-head">
        <div>
            <h1>Review Barang Masuk</h1>
            <p>Validasi detail penerimaan barang sebelum menentukan keputusan.</p>
        </div>
        <a href="{{ route('barang-masuk.index') }}" class="btn btn-ghost">Kembali ke Barang Masuk</a>
    </div>

    <article class="panel-card">
        <div class="grid gap-6 text-sm text-slate-600 dark:text-slate-400">
            <!-- Header Information -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between rounded-xl border border-slate-200 bg-white/50 p-4 dark:border-slate-800 dark:bg-slate-900/50">
                <div class="flex gap-8">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Kode Penerimaan</p>
                        <p class="mt-1 font-['Poppins'] text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">{{ $incoming->kode }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Supplier</p>
                        <p class="mt-1 text-lg font-bold text-slate-700 dark:text-slate-200">{{ $incoming->supplier->nama ?? '-' }}</p>
                    </div>
                </div>
                <div class="flex flex-col items-start gap-4 sm:items-end">
                    <div class="text-left sm:text-right">
                        <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Diinput Oleh & Waktu</p>
                        <p class="mt-0.5 text-sm font-medium text-slate-800 dark:text-slate-200">{{ $incoming->user->nama ?? '-' }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $incoming->tanggal_pesan ? $incoming->tanggal_pesan->format('d M Y, H:i') : '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Content Split (Struk & Tabel) -->
            <div class="grid gap-6 lg:grid-cols-[280px_minmax(0,1fr)]">
                <!-- Foto Struk -->
                <div class="flex items-start">
                    <div class="w-full overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700">
                        <div class="border-b border-slate-100 bg-slate-50 px-3 py-2 dark:border-slate-800 dark:bg-slate-900/50">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Foto Struk / Nota</p>
                        </div>
                        <div class="grid aspect-[3/4] w-full place-items-center bg-slate-100 dark:bg-slate-800">
                            @if($incoming->foto_struk)
                                <img src="{{ asset('storage/' . $incoming->foto_struk) }}" alt="Struk" class="max-w-full max-h-full object-contain">
                            @else
                                <div class="text-center text-slate-400">
                                    <svg class="mx-auto h-12 w-12 opacity-50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <p class="mt-2 text-xs">Tidak ada struk</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Rincian Daftar Barang -->
                <div class="rounded-xl border border-slate-200 dark:border-slate-800 h-fit">
                    <div class="border-b border-slate-100 bg-slate-50 px-4 py-3 dark:border-slate-800 dark:bg-slate-900/50">
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Rincian Barang yang Diterima ({{ $incoming->detail->count() }} Produk)</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-slate-100 bg-white dark:border-slate-800 dark:bg-transparent">
                                    <th class="px-4 py-2.5 text-xs font-semibold text-slate-500 dark:text-slate-400">Nama Barang</th>
                                    <th class="px-4 py-2.5 text-right text-xs font-semibold text-slate-500 dark:text-slate-400">Jml/Qty</th>
                                    <th class="px-4 py-2.5 text-right text-xs font-semibold text-slate-500 dark:text-slate-400">Est. Harga Satuan</th>
                                    <th class="px-4 py-2.5 text-right text-xs font-semibold text-slate-500 dark:text-slate-400">Total Nilai</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @php $grandTotal = 0; @endphp
                                @foreach($incoming->detail as $item)
                                    @php 
                                        $subtotal = $item->jumlah * $item->harga; 
                                        $grandTotal += $subtotal;
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="flex flex-col">
                                                <span class="font-medium text-slate-800 dark:text-slate-200">{{ $item->produk->nama ?? '-' }}</span>
                                                <span class="text-[10px] text-slate-400">SKU: {{ $item->produk->sku ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-right text-slate-600 dark:text-slate-300 font-bold">{{ $item->jumlah }} Unit</td>
                                        <td class="px-4 py-3 text-right text-slate-600 dark:text-slate-300">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-right font-medium text-slate-800 dark:text-slate-200">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-t-2 border-slate-100 dark:border-slate-800 bg-slate-50/30">
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-right font-medium text-slate-600 dark:text-slate-400 uppercase text-xs tracking-wider">Total Nilai Dokumen</td>
                                    <td class="px-4 py-3 text-right font-bold text-slate-800 dark:text-slate-200 text-lg">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approval Form / Status Footer -->
        @if(!$incoming->isDisetujui() && strtolower($incoming->status) !== 'ditolak')
            <form action="{{ route('barang-masuk.update-status', $incoming->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mt-6 border-t border-slate-100 pt-5 dark:border-slate-800">
                    <label for="incomingNotes" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Catatan Review (Opsional)</label>
                    <textarea name="catatan" id="incomingNotes" class="field min-h-[80px] w-full py-2" placeholder="Tambahkan catatan mengapa disetujui atau ditolak...">{{ old('catatan', $incoming->catatan) }}</textarea>
                </div>

                <div class="form-actions mt-4">
                    <button type="submit" name="status" value="Ditolak" class="btn btn-ghost !border-rose-200 !text-rose-600 hover:!bg-rose-50 dark:!border-rose-900/50 dark:!bg-slate-900 dark:!text-rose-500 dark:hover:!bg-rose-900/20">
                        Tolak Dokumen
                    </button>
                    <button type="submit" name="status" value="Disetujui" class="btn btn-primary bg-emerald-600 border-emerald-600 hover:bg-emerald-700 hover:border-emerald-700 focus:ring-emerald-500/20 dark:bg-emerald-600 dark:border-emerald-600 dark:hover:bg-emerald-500">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        ACC & Tambah Stok
                    </button>
                </div>
            </form>
        @else
            <div class="mt-6 border-t border-slate-100 pt-5 dark:border-slate-800 flex justify-between items-center">
                <div>
                    <p class="text-sm font-bold {{ $incoming->isDisetujui() ? 'text-emerald-600' : 'text-rose-600' }} uppercase tracking-widest">
                        Status: {{ $incoming->status }}
                    </p>
                    <p class="text-xs text-slate-400">Diproses pada: {{ $incoming->tanggal_terima ? $incoming->tanggal_terima->format('d M Y, H:i') : '-' }}</p>
                </div>
                @if($incoming->catatan)
                    <div class="max-w-md text-right">
                        <p class="text-xs font-bold text-slate-400 uppercase mb-1">Catatan pemilik:</p>
                        <p class="text-sm text-slate-700 dark:text-slate-300 italic">"{{ $incoming->catatan }}"</p>
                    </div>
                @endif
            </div>
        @endif
    </article>
</section>
@endsection
