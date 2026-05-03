<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Produk</th>
                <th>Tipe</th>
                <th>Jumlah</th>
                <th>Keterangan</th>
                <th>Doc Ref.</th>
                <th class="text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td class="whitespace-nowrap">
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-700">{{ $log->tanggal ? $log->tanggal->format('d M Y') : '-' }}</span>
                            <span class="text-[10px] text-slate-400 font-medium">{{ $log->tanggal ? $log->tanggal->format('H:i') : '' }}</span>
                        </div>
                    </td>
                    <td class="font-medium text-slate-700 dark:text-slate-200">{{ $log->produk->nama ?? 'Produk Terhapus' }}</td>
                    <td>
                        @if($log->tipe === 'masuk')
                            <span class="status-pill success">Masuk</span>
                        @elseif($log->tipe === 'keluar')
                            <span class="status-pill danger">Keluar</span>
                        @else
                            <span class="status-pill warning">{{ ucfirst($log->tipe) }}</span>
                        @endif
                    </td>
                    <td class="font-bold {{ $log->tipe === 'masuk' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $log->tipe === 'masuk' ? '+' : '-' }}{{ $log->jumlah }}
                    </td>
                    <td class="max-w-xs truncate">{{ $log->keterangan }}</td>
                    <td>
                        @if($log->transaksi)
                            <a href="{{ route('transaksi.show', $log->transaksi) }}" class="underline decoration-slate-300">#{{ $log->transaksi->kode }}</a>
                        @elseif($log->barangMasuk)
                            <a href="{{ route('barang-masuk.show', $log->barangMasuk) }}" class="underline decoration-slate-300">#{{ $log->barangMasuk->kode }}</a>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">
                        <a href="{{ route('log-stok.show', $log) }}" class="link-btn more">Detail</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-slate-500 py-8">Belum ada riwayat pergerakan stok.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $logs->links() }}
</div>
