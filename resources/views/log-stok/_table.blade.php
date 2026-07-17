<div class="table-wrap" id="logStokTableContainer">
    <table>
        <thead>
            <tr>
                <th>TANGGAL</th>
                <th>PRODUK</th>
                <th>TIPE</th>
                <th>JUMLAH</th>
                <th>KETERANGAN</th>
                <th>DOC REF.</th>
                <th class="text-right">AKSI</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movements as $mov)
                <tr>
                    <td>
                        <div class="flex flex-col">
                            <span class="font-semibold text-slate-700 dark:text-slate-200">
                                {{ $mov->tanggal->translatedFormat('d M Y') }}
                            </span>
                            <span class="text-[10px] text-slate-400 dark:text-zinc-500 font-mono">
                                {{ $mov->tanggal->format('H:i') }}
                            </span>
                        </div>
                    </td>
                    <td>
                        <div class="flex flex-col">
                            <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $mov->produk_nama }}</span>
                            <span class="text-[10px] text-slate-400 dark:text-zinc-500 font-mono uppercase">{{ $mov->produk_sku }}</span>
                        </div>
                    </td>
                    <td>
                        @if(strtolower($mov->tipe) === 'masuk')
                            <span class="tag success inline-flex items-center bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-700 px-2 py-0.5 rounded text-[11px] font-semibold">
                                Masuk
                            </span>
                        @else
                            <span class="tag info inline-flex items-center bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 border border-blue-300 dark:border-blue-700 px-2 py-0.5 rounded text-[11px] font-semibold">
                                Keluar
                            </span>
                        @endif
                    </td>
                    <td>
                        @if(strtolower($mov->tipe) === 'masuk')
                            <span class="font-bold text-emerald-500 dark:text-emerald-400 text-sm">+{{ $mov->jumlah }}</span>
                        @else
                            <span class="font-bold text-red-500 dark:text-red-400 text-sm">{{ $mov->jumlah }}</span>
                        @endif
                    </td>
                    <td class="text-slate-600 dark:text-slate-300">{{ $mov->keterangan }}</td>
                    <td>
                        @if($mov->doc_type === 'transaksi')
                            <a href="{{ route('transaksi.show', $mov->doc_id) }}" class="font-mono text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                                #{{ $mov->doc_ref }}
                            </a>
                        @else
                            <a href="{{ route('barang-masuk.show', $mov->doc_id) }}" class="font-mono text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                                #{{ $mov->doc_ref }}
                            </a>
                        @endif
                    </td>
                    <td class="text-right">
                        @if($mov->doc_type === 'transaksi')
                            <a href="{{ route('transaksi.show', $mov->doc_id) }}" class="btn btn-ghost btn-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 text-xs font-semibold">
                                Detail
                            </a>
                        @else
                            <a href="{{ route('barang-masuk.show', $mov->doc_id) }}" class="btn btn-ghost btn-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 text-xs font-semibold">
                                Detail
                            </a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-slate-500 dark:text-zinc-400 py-12">Tidak ada log pergerakan stok yang ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $movements->links('pagination::tailwind') }}
</div>
