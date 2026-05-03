<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    /**
     * Get all application settings.
     */
    public function index(): JsonResponse
    {
        $setting = Setting::first();

        return response()->json([
            'success' => true,
            'message' => 'Daftar pengaturan berhasil diambil',
            'data' => $setting
        ], 200);
    }
}
