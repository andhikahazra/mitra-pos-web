<div class="table-wrap" id="supplierTableContainer">
    <table>
        <thead>
            <tr>
                <th>Nama Supplier</th>
                <th>No. Telepon</th>
                <th>Alamat</th>
                <th class="text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($suppliers as $supplier)
                <tr>
                    <td class="font-medium text-slate-700 dark:text-slate-200">{{ $supplier->nama }}</td>
                    <td>{{ $supplier->no_telp ?? '-' }}</td>
                    <td class="max-w-xs truncate">{{ $supplier->alamat ?? '-' }}</td>
                    <td class="text-right">
                        <div class="row-actions justify-end">
                            <a class="link-btn more" href="{{ route('supplier.show', $supplier) }}" title="Detail">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a class="link-btn edit" href="{{ route('supplier.edit', $supplier) }}" title="Edit">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('supplier.destroy', $supplier) }}" class="inline" onsubmit="return confirm('Hapus supplier ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="link-btn danger" title="Hapus">
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-slate-500 py-8">Tidak ada data supplier.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="mt-4">
        {{ $suppliers->links() }}
    </div>
</div>
