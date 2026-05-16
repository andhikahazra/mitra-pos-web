<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;

class CustomerController extends Controller
{
    public function history()
    {
        $customers = Transaksi::select('nama_pelanggan', 'no_hp_pelanggan')
            ->whereNotNull('nama_pelanggan')
            ->where('nama_pelanggan', '!=', '')
            ->orderBy('id', 'desc')
            ->get()
            ->unique('nama_pelanggan')
            ->values();

        return response()->json([
            'success' => true,
            'data' => $customers
        ]);
    }
}
