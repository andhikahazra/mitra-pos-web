<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::orderBy('nama', 'asc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar supplier berhasil diambil',
            'data' => $suppliers
        ], 200);
    }
}
