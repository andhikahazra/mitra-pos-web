<div class="table-wrap" id="stokBatchTableContainer">
    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Kategori</th>
                <th>Total Stok Global</th>
                <th>Batch Aktif</th>
                <th class="text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                <tr>
                    <td>
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-700 dark:text-slate-200">{{ $product->nama }}</span>
                            <span class="text-[10px] text-slate-400 font-mono uppercase">{{ $product->sku }}</span>
                        </div>
                    </td>
                    <td><span class="tag neutral">{{ $product->kategori->nama ?? '-' }}</span></td>
                    <td>
                        <div class="flex items-center gap-2">
                            <span class="text-lg font-extrabold {{ ($product->total_qty_sisa ?? 0) > 0 ? 'text-slate-800' : 'text-slate-300' }}">
                                {{ number_format($product->total_qty_sisa ?? 0, 0, ',', '.') }}
                            </span>
                            <span class="text-xs text-slate-400">Unit</span>
                        </div>
                    </td>
                    <td>
                        <span class="status-pill {{ ($product->total_batch_aktif ?? 0) > 0 ? 'info' : 'neutral' }}">
                            {{ $product->total_batch_aktif ?? 0 }} Batch
                        </span>
                    </td>
                    <td class="text-right">
                        <a href="{{ route('stok-batch.show', $product) }}" class="btn btn-ghost btn-sm">
                            Detail
                            <svg viewBox="0 0 24 24" class="h-3 w-3 inline ml-1" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M13 7l5 5-5 5M6 12h12"/></svg>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-slate-500 py-12">Tidak ada data produk yang ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $products->links() }}
</div>
