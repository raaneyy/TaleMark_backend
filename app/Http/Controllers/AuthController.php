<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class AuthController extends Controller
{
    public function loginWithGoogle(Request $request)
    {
        // 1. Validasi bahwa Flutter mengirimkan access_token
        $request->validate([
            'access_token' => 'required|string',
        ]);

        try {
            // 2. Verifikasi token ke server Google menggunakan Socialite
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->userFromToken($request->access_token);

            // 3. Cari user di tabel 'users' berdasarkan email, atau buat baru jika belum ada
            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'password' => bcrypt(Str::random(16)), // Password acak karena login via Google
                ]
            );

            // 4. Buat API Token (Sanctum) untuk disimpan di aplikasi Flutter
            $token = $user->createToken('talemark-android')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Login Google berhasil',
                'user' => $user,
                'token' => $token,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal memverifikasi token Google',
                'error' => $e->getMessage(),
            ], 401);
        }
    }
}
