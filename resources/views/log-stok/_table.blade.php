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
            @php
                $grouped = $movements->groupBy(fn($m) => $m->doc_type . '_' . $m->doc_id);
            @endphp
            @forelse($grouped as $key => $group)
                @php
                    $first = $group->first();
                    $itemCount = $group->count();
                    $totalQty = $group->sum('jumlah');
                    $isIncoming = strtolower($first->tipe) === 'masuk';
                @endphp
                @foreach($group as $idx => $mov)
                    <tr class="@if($idx === 0) border-l-2 @endif @if($isIncoming) border-l-emerald-400 @else border-l-blue-400 @endif">
                        <td>
                            @if($idx === 0)
                                <div class="flex flex-col">
                                    <span class="font-semibold text-slate-700 dark:text-slate-200">
                                        {{ $mov->tanggal->translatedFormat('d M Y') }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 dark:text-zinc-500 font-mono">
                                        {{ $mov->tanggal->format('H:i') }}
                                    </span>
                                </div>
                            @else
                                <span class="text-[10px] text-slate-400 dark:text-zinc-500 font-mono">↳</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                @if($idx === 0)
                                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $mov->produk_nama }}</span>
                                @else
                                    <span class="text-sm text-slate-600 dark:text-zinc-300 pl-3 border-l border-slate-200 dark:border-zinc-700">{{ $mov->produk_nama }}</span>
                                @endif
                                <span class="text-[10px] text-slate-400 dark:text-zinc-500 font-mono uppercase">{{ $mov->produk_sku }}</span>
                            </div>
                            @if($idx === 0 && $itemCount > 1)
                                <span class="mt-1 inline-flex items-center gap-1 text-[10px] font-medium text-slate-400 dark:text-zinc-500">
                                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    1 invoice &middot; {{ $itemCount }} item
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($isIncoming)
                                <span class="tag success inline-flex items-center bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-700 px-2 py-0.5 rounded text-[11px] font-semibold">
                                    Masuk
                                </span>
                            @else
                                <span class="tag info inline-flex items-center bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 border border-blue-300 dark:border-blue-700 px-2 py-0.5 rounded text-[11px] font-semibold">
                                    Keluar
                                </span>
                            @endif
                        </td>
                        <td class="font-bold text-sm {{ $isIncoming ? 'text-emerald-500 dark:text-emerald-400' : 'text-red-500 dark:text-red-400' }}">
                            {{ $isIncoming ? '+' : '' }}{{ $mov->jumlah }}
                        </td>
                        <td class="text-slate-600 dark:text-slate-300">
                            @if($idx === 0)
                                {{ $mov->keterangan }}
                            @else
                                <span class="text-slate-400 dark:text-zinc-500 text-xs">dari invoice #{{ $first->doc_ref }}</span>
                            @endif
                        </td>
                        <td>
                            @if($idx === 0)
                                @if($mov->doc_type === 'transaksi')
                                    <a href="{{ route('transaksi.show', $mov->doc_id) }}" class="font-mono text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                                        #{{ $mov->doc_ref }}
                                    </a>
                                @else
                                    <a href="{{ route('barang-masuk.show', $mov->doc_id) }}" class="font-mono text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                                        #{{ $mov->doc_ref }}
                                    </a>
                                @endif
                            @endif
                        </td>
                        <td class="text-right">
                            @if($idx === 0)
                                @if($mov->doc_type === 'transaksi')
                                    <a href="{{ route('transaksi.show', $mov->doc_id) }}" class="btn btn-ghost btn-sm text-slate-700 dark:text-zinc-300 hover:bg-blue-50 hover:text-blue-700 dark:hover:bg-blue-900/20 dark:hover:text-blue-300 text-xs font-semibold">
                                        Detail
                                    </a>
                                @else
                                    <a href="{{ route('barang-masuk.show', $mov->doc_id) }}" class="btn btn-ghost btn-sm text-slate-700 dark:text-zinc-300 hover:bg-blue-50 hover:text-blue-700 dark:hover:bg-blue-900/20 dark:hover:text-blue-300 text-xs font-semibold">
                                        Detail
                                    </a>
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
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
