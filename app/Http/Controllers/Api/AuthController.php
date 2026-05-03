<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Handle user login and create token.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            
            // Periksa role Karyawan
            if ($user->role !== User::ROLE_KARYAWAN) {
                Auth::logout();
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya akun Karyawan yang dapat masuk ke aplikasi ini.',
                ], 403);
            }

            if (!$user->status) {
                Auth::logout();
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda dinonaktifkan. Silakan hubungi pemilik.',
                ], 403);
            }

            $token = $user->createToken('MitraPOSToken')->accessToken;

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah',
            ], 401);
        }
    }

    /**
     * Handle user logout and revoke token.
     */
    public function logout(Request $request)
    {
        if (Auth::guard('api')->check()) {
            $request->user()->token()->revoke();
            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal logout, user tidak terautentikasi'
        ], 401);
    }

    /**
     * Get authenticated user data.
     */
    public function profile(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ], 200);
    }
}
