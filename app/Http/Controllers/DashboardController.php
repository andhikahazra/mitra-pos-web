<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $products = Produk::query()
            ->with([
                'kategori:id,nama',
                'rop:id,produk_id,safety_stock,lead_time,reorder_point',
            ])
            ->orderBy('nama')
            ->get(['id', 'nama', 'stok', 'kategori_id']);

        $transactions = Transaksi::query()
            ->with(['user:id,nama', 'detail_transaksi:id,transaksi_id,jumlah,harga'])
            ->where('metode_pembayaran', '!=', 'Internal')
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();

        // ROP rows untuk alerts dan metrics
        $ropRows = $products->map(function (Produk $product): array {
            $ropValue    = (int) ($product->rop->reorder_point ?? 0);
            $safetyStock = (int) ($product->rop->safety_stock ?? 0);
            $stock       = (int) $product->stok;

            if ($stock <= $ropValue) {
                $status = 'harus restock';
            } elseif ($stock <= $ropValue + ($safetyStock * 0.5)) {
                $status = 'hampir habis';
            } else {
                $status = 'aman';
            }

            return [
                'name'        => $product->nama,
                'stock'       => $stock,
                'safetyStock' => $safetyStock,
                'leadTime'    => (float) ($product->rop->lead_time ?? 0),
                'rop'         => $ropValue,
                'status'      => $status,
            ];
        })->values();

        // Latest 5 transaksi untuk tabel dashboard
        $latestTransactions = $transactions->take(5)->map(function (Transaksi $trx): array {
            return [
                'invoice'  => $trx->kode,
                'date'     => $trx->tanggal->format('Y-m-d H:i'),
                'cashier'  => $trx->user->nama ?? '-',
                'items'    => (int) $trx->detail_transaksi->sum('jumlah'),
                'total'    => (float) $trx->total_harga,
                'status'   => $trx->status,
            ];
        })->values();

        // Chart: penjualan 7 hari
        $salesByDate = $transactions
            ->groupBy(fn (Transaksi $trx): string => $trx->tanggal->toDateString())
            ->sortKeysDesc()
            ->take(7)
            ->reverse()
            ->map(fn (Collection $rows): array => [
                'label' => $rows->first()->tanggal->format('d M'),
                'total' => (float) $rows->sum('total_harga'),
            ])
            ->values();

        // Chart: stok per kategori
        $allProducts = Produk::query()->with('kategori:id,nama')->get(['id', 'stok', 'kategori_id']);
        $stockByCategory = $allProducts
            ->groupBy(fn (Produk $p): string => $p->kategori->nama ?? 'Lainnya')
            ->map(fn (Collection $rows): int => (int) $rows->sum('stok'))
            ->sortDesc()
            ->take(6)
            ->map(fn (int $stock, string $category): array => [
                'label' => $category,
                'stock' => $stock,
            ])
            ->values();

        $today              = now()->toDateString();
        $todayTransactions  = $transactions->filter(function ($trx) use ($today) {
            return $trx->tanggal->toDateString() === $today;
        });
        $pendingIncoming    = BarangMasuk::where('status', 'menunggu')->count();

        $payload = [
            'metrics' => [
                'omzetToday'      => (float) $todayTransactions->sum('total_harga'),
                'trxToday'        => (int) $todayTransactions->count(),
                'criticalRop'     => (int) $ropRows->where('status', 'harus restock')->count(),
                'pendingIncoming' => (int) $pendingIncoming,
            ],
            'latestTransactions' => $latestTransactions,
            'alerts'             => $this->buildAlerts($ropRows),
            'charts'             => [
                'sales7days'      => $salesByDate,
                'stockByCategory' => $stockByCategory,
            ],
        ];

        return view('dashboard.index', [
            'dashboardPayload' => $payload,
        ]);
    }

    private function buildAlerts(Collection $ropRows): array
    {
        $alerts = [];

        $restockItems = $ropRows->where('status', 'harus restock');
        if ($restockItems->isNotEmpty()) {
            $count = $restockItems->count();
            $names = $restockItems->take(2)->pluck('name')->implode(', ');
            $suffix = $count > 2 ? ' dan ' . ($count - 2) . ' lainnya' : '';
            
            $alerts[] = [
                'level' => 'danger',
                'label' => 'Restock',
                'text'  => $count . ' produk (' . $names . $suffix . ') menyentuh batas ROP. Peringatan segera restock!',
            ];
        }

        $hampirHabisItems = $ropRows->where('status', 'hampir habis');
        if ($hampirHabisItems->isNotEmpty()) {
            $count = $hampirHabisItems->count();
            $names = $hampirHabisItems->take(2)->pluck('name')->implode(', ');
            $suffix = $count > 2 ? ' dan ' . ($count - 2) . ' lainnya' : '';
            
            $alerts[] = [
                'level' => 'warning',
                'label' => 'Warning',
                'text'  => $count . ' produk (' . $names . $suffix . ') mendekati batas kritis.',
            ];
        }

        $pendingCount = BarangMasuk::where('status', 'menunggu')->count();
        if ($pendingCount > 0) {
            $alerts[] = [
                'level' => 'info',
                'label' => 'Action',
                'text'  => $pendingCount . ' dokumen barang masuk menunggu ACC.',
            ];
        }

        if ($restockItems->isEmpty() && $hampirHabisItems->isEmpty()) {
            $alerts[] = [
                'level' => 'neutral',
                'label' => 'Info',
                'text'  => 'Seluruh stok utama dalam kondisi aman.',
            ];
        }

        return array_slice($alerts, 0, 4);
    }
}
