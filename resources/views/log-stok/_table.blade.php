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
                            <span class="font-bold text-slate-700 dark:text-slate-200">
                                {{ $mov->tanggal->translatedFormat('d M Y') }}
                            </span>
                            <span class="text-[10px] text-slate-400 font-mono">
                                {{ $mov->tanggal->format('H:i') }}
                            </span>
                        </div>
                    </td>
                    <td>
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-700 dark:text-slate-200">{{ $mov->produk_nama }}</span>
                            <span class="text-[10px] text-slate-400 font-mono uppercase">{{ $mov->produk_sku }}</span>
                        </div>
                    </td>
                    <td>
                        @if(strtolower($mov->tipe) === 'masuk')
                            <span class="tag success" style="background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">
                                Masuk
                            </span>
                        @else
                            <span class="tag info" style="background-color: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">
                                Keluar
                            </span>
                        @endif
                    </td>
                    <td>
                        @if(strtolower($mov->tipe) === 'masuk')
                            <span style="color: #10b981; font-weight: 800; font-size: 14px;">+{{ $mov->jumlah }}</span>
                        @else
                            <span style="color: #ef4444; font-weight: 800; font-size: 14px;">{{ $mov->jumlah }}</span>
                        @endif
                    </td>
                    <td class="text-slate-600 dark:text-slate-300 font-medium">{{ $mov->keterangan }}</td>
                    <td>
                        @if($mov->doc_type === 'transaksi')
                            <a href="{{ route('transaksi.show', $mov->doc_id) }}" class="font-mono text-xs font-bold text-blue-600 hover:underline" style="color: #2563eb;">
                                #{{ $mov->doc_ref }}
                            </a>
                        @else
                            <a href="{{ route('barang-masuk.show', $mov->doc_id) }}" class="font-mono text-xs font-bold text-blue-600 hover:underline" style="color: #2563eb;">
                                #{{ $mov->doc_ref }}
                            </a>
                        @endif
                    </td>
                    <td class="text-right">
                        @if($mov->doc_type === 'transaksi')
                            <a href="{{ route('transaksi.show', $mov->doc_id) }}" class="btn btn-ghost btn-sm text-blue-600 hover:text-blue-800" style="font-size: 12px; font-weight: bold; color: #2563eb; text-decoration: none;">
                                Detail
                            </a>
                        @else
                            <a href="{{ route('barang-masuk.show', $mov->doc_id) }}" class="btn btn-ghost btn-sm text-blue-600 hover:text-blue-800" style="font-size: 12px; font-weight: bold; color: #2563eb; text-decoration: none;">
                                Detail
                            </a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-slate-500 py-12">Tidak ada log pergerakan stok yang ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $movements->links() }}
</div>
