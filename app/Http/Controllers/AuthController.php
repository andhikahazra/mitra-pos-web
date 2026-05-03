<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        $todayStr = \Carbon\Carbon::now()->toDateString();
        $trxToday = \App\Models\Transaksi::whereDate('tanggal', $todayStr)->count();
        $pendingApproval = \App\Models\BarangMasuk::where('status', 'menunggu')->count();

        return view('auth.login', compact('trxToday', 'pendingApproval'));
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials['status'] = true;

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Hanya Pemilik (Pemilik) yang boleh masuk ke Dashboard Web
            if ($user->role !== \App\Models\User::ROLE_PEMILIK) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Hanya akun Pemilik yang dapat mengakses Dashboard Web.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            return redirect()->intended(route('pemilik.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email atau password tidak valid.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
