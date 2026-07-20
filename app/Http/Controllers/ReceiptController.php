<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReceiptController extends Controller
{
    public function show(string $kode)
    {
        $transaksi = Transaksi::with(['user:id,nama', 'detail_transaksi.produk:id,nama'])
            ->where('kode', $kode)
            ->firstOrFail();

        $items = $transaksi->detail_transaksi->groupBy('produk_id')->map(function ($details) {
            $first = $details->first();
            return [
                'name'     => $first->produk->nama ?? '-',
                'qty'      => (int) $details->sum('jumlah'),
                'unit'     => 'Pcs',
                'price'    => (float) $first->harga,
                'subtotal' => (float) $details->sum('subtotal'),
            ];
        })->values();

        $setting = Setting::first();
        $alamat = collect($setting->alamat_toko ?? [])->filter()->implode(', ');

        $subtotal   = (float) $transaksi->total_harga;
        $admin      = (float) $transaksi->biaya_admin;
        $method     = $transaksi->metode_pembayaran;
        $grandTotal = $subtotal + $admin;

        $title        = "Nota Digital - {$transaksi->kode}";
        $storeName    = $setting->nama_toko ?? 'MitraPOS';
        $ogTitle      = "Nota {$transaksi->kode} | {$storeName}";
        $ogDescription = "Total: Rp " . number_format($grandTotal, 0, ',', '.') . " | {$method}";
        $ogImage      = route('nota.og-image', $transaksi->kode);
        $ogUrl        = route('nota.show', $transaksi->kode);

        $totals = [
            'subtotal'    => $subtotal,
            'admin'       => $admin,
            'method'      => $method,
            'grand_total' => $grandTotal,
        ];

        return view('nota.show', compact(
            'transaksi', 'items', 'setting', 'alamat', 'totals',
            'title', 'ogTitle', 'ogDescription', 'ogImage', 'ogUrl'
        ));
    }

    /**
     * Generate receipt OG image.
     */
    public function ogImage(string $kode): Response
    {
        $transaksi = Transaksi::with(['detail_transaksi.produk:id,nama'])
            ->where('kode', $kode)
            ->firstOrFail();

        $setting    = Setting::first();
        $storeName  = $setting->nama_toko ?? 'MitraPOS';
        $grandTotal = (float) $transaksi->total_harga + (float) $transaksi->biaya_admin;
        $method     = $transaksi->metode_pembayaran;

        $width  = 600;
        $height = 315;
        $img    = imagecreatetruecolor($width, $height);

        $bg         = imagecolorallocate($img, 255, 255, 255);
        $dark       = imagecolorallocate($img, 15, 23, 42);
        $slate      = imagecolorallocate($img, 100, 116, 139);
        $emerald    = imagecolorallocate($img, 16, 185, 129);
        $border     = imagecolorallocate($img, 226, 232, 240);

        imagefill($img, 0, 0, $bg);

        imagerectangle($img, 0, 0, $width - 1, $height - 1, $border);

        imagefilledrectangle($img, 1, 1, $width - 2, 56, $dark);

        $font = 5;
        $textW = imagefontwidth($font) * strlen($storeName);
        imagestring($img, $font, (int) (($width - $textW) / 2), 20, $storeName, $bg);

        $date = $transaksi->tanggal?->format('d M Y H:i') ?? '-';
        $infoY = 75;

        imagestring($img, 4, 30, $infoY, "No: {$transaksi->kode}", $slate);
        imagestring($img, 4, 30, $infoY + 22, "Tgl: {$date}", $slate);
        imagestring($img, 4, 30, $infoY + 44, "Byr: {$method}", $slate);

        $rightX = $width - 30;
        $totalLabel = "TOTAL";
        $totalLabelW = imagefontwidth(4) * strlen($totalLabel);
        imagestring($img, 4, $rightX - $totalLabelW - 80, $infoY, $totalLabel, $slate);

        $totalText = "Rp " . number_format($grandTotal, 0, ',', '.');
        $totalTextW = imagefontwidth(5) * strlen($totalText);
        imagestring($img, 5, $rightX - $totalTextW, $infoY + 22, $totalText, $emerald);

        $itemsY = $infoY + 80;
        imagerectangle($img, 30, $itemsY, $width - 30, $height - 30, $border);
        $y = $itemsY + 10;
        $count = 0;
        foreach ($transaksi->detail_transaksi as $detail) {
            if ($count >= 4) {
                imagestring($img, 4, 40, $y, "...", $slate);
                break;
            }
            $name = $detail->produk->nama ?? '-';
            imagestring($img, 4, 40, $y, $name, $dark);
            $sub = "Rp " . number_format($detail->subtotal, 0, ',', '.');
            $subW = imagefontwidth(4) * strlen($sub);
            imagestring($img, 4, $rightX - $subW - 10, $y, $sub, $slate);
            $y += 22;
            $count++;
        }

        ob_start();
        imagepng($img);
        $imageData = ob_get_clean();
        imagedestroy($img);

        return response($imageData, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}