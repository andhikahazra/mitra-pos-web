<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $range = $request->query('range', 'today');
        
        $startDate = null;
        $endDate = null;
        
        if ($range === 'today') {
            $startDate = now()->startOfDay();
            $endDate = now()->endOfDay();
        } elseif ($range === '7d') {
            $startDate = now()->subDays(6)->startOfDay();
            $endDate = now()->endOfDay();
        } elseif ($range === '1m') {
            $startDate = now()->subDays(29)->startOfDay();
            $endDate = now()->endOfDay();
        } elseif ($range === 'custom') {
            $startStr = $request->query('start_date');
            $endStr = $request->query('end_date');
            if ($startStr && $endStr) {
                $startDate = \Illuminate\Support\Carbon::parse($startStr)->startOfDay();
                $endDate = \Illuminate\Support\Carbon::parse($endStr)->endOfDay();
                
                // Limit custom range to max 90 days to prevent chart/query overload
                if ($startDate->diffInDays($endDate) > 90) {
                    $endDate = $startDate->copy()->addDays(90)->endOfDay();
                }
            } else {
                $startDate = now()->startOfDay();
                $endDate = now()->endOfDay();
                $range = 'today';
            }
        } else {
            $startDate = now()->startOfDay();
            $endDate = now()->endOfDay();
            $range = 'today';
        }

        // Determine query range for loading transactions.
        // For 'today' & '7d', load last 7 days for trend line chart.
        // For '1m', load last 30 days.
        // For 'custom', load the exact custom range.
        $queryStartDate = null;
        if ($range === 'today' || $range === '7d') {
            $queryStartDate = now()->subDays(6)->startOfDay();
        } elseif ($range === '1m') {
            $queryStartDate = now()->subDays(29)->startOfDay();
        } else {
            $queryStartDate = $startDate;
        }
        $queryEndDate = $range === 'custom' ? $endDate : now()->endOfDay();

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
            ->whereBetween('tanggal', [$queryStartDate, $queryEndDate])
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

        // Filter transactions strictly to target range for metrics & latest transactions table
        $rangeTransactions = $transactions->filter(function (Transaksi $trx) use ($startDate, $endDate) {
            return $trx->tanggal->between($startDate, $endDate);
        });

        // Latest 5 transaksi untuk tabel dashboard (filtered by current active range)
        $latestTransactions = $rangeTransactions->take(5)->map(function (Transaksi $trx): array {
            return [
                'invoice'  => $trx->kode,
                'date'     => $trx->tanggal->format('Y-m-d H:i'),
                'cashier'  => $trx->user->nama ?? '-',
                'items'    => (int) $trx->detail_transaksi->sum('jumlah'),
                'total'    => (float) $trx->total_harga,
                'status'   => $trx->status,
            ];
        })->values();

        // Chart: penjualan (filled with all dates in the range, zeroing empty days)
        $periodStart = $queryStartDate->copy();
        $periodEnd = $queryEndDate->copy();
        
        $dates = [];
        for ($date = $periodStart->copy(); $date->lte($periodEnd); $date->addDay()) {
            $dates[$date->toDateString()] = [
                'label' => $date->format('d M'),
                'total' => 0.0,
            ];
        }
        
        $grouped = $transactions->groupBy(fn (Transaksi $trx): string => $trx->tanggal->toDateString());
        foreach ($grouped as $dateStr => $trxGroup) {
            if (isset($dates[$dateStr])) {
                $dates[$dateStr]['total'] = (float) $trxGroup->sum('total_harga');
            }
        }
        
        $salesByDate = collect(array_values($dates));

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

        $pendingIncoming = BarangMasuk::where('status', 'menunggu')->count();

        $payload = [
            'range'   => $range,
            'metrics' => [
                'omzetToday'      => (float) $rangeTransactions->sum('total_harga'),
                'trxToday'        => (int) $rangeTransactions->count(),
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
