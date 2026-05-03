<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $setting = Setting::first();
        return view('settings.index', compact('setting'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_toko' => 'required|string|max:255',
            'no_hp' => 'nullable|string|max:20',
            'biaya_admin_qris' => 'required|numeric|min:0',
            'footer_nota' => 'nullable|string',
            // Validasi Alamat (Pecahan)
            'alamat_jalan' => 'nullable|string',
            'alamat_kota' => 'nullable|string',
            'alamat_provinsi' => 'nullable|string',
            // Validasi Deskripsi (Pecahan)
            'deskripsi_slogan' => 'nullable|string|max:255',
            'deskripsi_keterangan' => 'nullable|string',
            // Validasi Bank Slot 1
            'bank_name_1' => 'nullable|string|max:100',
            'bank_no_1' => 'nullable|string|max:50',
            'bank_Pemilik_1' => 'nullable|string|max:100',
            // Validasi Bank Slot 2
            'bank_name_2' => 'nullable|string|max:100',
            'bank_no_2' => 'nullable|string|max:50',
            'bank_Pemilik_2' => 'nullable|string|max:100',
        ]);

        $data = $request->only(['nama_toko', 'no_hp', 'biaya_admin_qris', 'footer_nota']);
        
        // Gabungkan data alamat
        $data['alamat_toko'] = [
            'jalan' => $request->alamat_jalan,
            'kota' => $request->alamat_kota,
            'provinsi' => $request->alamat_provinsi,
        ];

        // Gabungkan data deskripsi
        $data['deskripsi'] = [
            'slogan' => $request->deskripsi_slogan,
            'keterangan' => $request->deskripsi_keterangan,
        ];

        // Gabungkan data bank menjadi array
        $rekening = [];
        if ($request->filled('bank_name_1')) {
            $rekening[] = [
                'bank' => $request->bank_name_1,
                'no' => $request->bank_no_1,
                'nama' => $request->bank_Pemilik_1,
            ];
        }
        if ($request->filled('bank_name_2')) {
            $rekening[] = [
                'bank' => $request->bank_name_2,
                'no' => $request->bank_no_2,
                'nama' => $request->bank_Pemilik_2,
            ];
        }
        $data['rekening_bank'] = $rekening;

        $setting = Setting::first();
        $setting->update($data);

        return back()->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
