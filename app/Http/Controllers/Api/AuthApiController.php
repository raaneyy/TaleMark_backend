<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthApiController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan gunakan email lain atau masuk.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('talemark-mobile')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil',
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau kata sandi tidak cocok.'],
            ]);
        }

        $token = $user->createToken('talemark-mobile')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function loginWithGoogle(Request $request)
    {
        $idToken = $request->input('id_token');
        $accessToken = $request->input('access_token');
        $name = $request->input('name');
        $email = $request->input('email');

        // Verifikasi token ke server Google jika token tersedia
        if ($idToken) {
            try {
                $googleRes = Http::timeout(10)->get('https://oauth2.googleapis.com/tokeninfo', [
                    'id_token' => $idToken,
                ]);

                if ($googleRes->successful()) {
                    $googleData = $googleRes->json();
                    $email = $googleData['email'] ?? $email;
                    $name = $googleData['name'] ?? $name;
                }
            } catch (\Exception $e) {
                // Lewati verifikasi eksternal jika koneksi bermasalah
            }
        } elseif ($accessToken) {
            try {
                $googleRes = Http::timeout(10)->withToken($accessToken)
                    ->get('https://www.googleapis.com/oauth2/v3/userinfo');

                if ($googleRes->successful()) {
                    $googleData = $googleRes->json();
                    $email = $googleData['email'] ?? $email;
                    $name = $googleData['name'] ?? $name;
                }
            } catch (\Exception $e) {
                // Lewati verifikasi eksternal jika koneksi bermasalah
            }
        }

        if (empty($email)) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan data akun Google.',
            ], 422);
        }

        $name = $name ?: explode('@', $email)[0];

        // Cari pengguna berdasarkan email atau buat akun baru
        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(Str::random(24)),
                'email_verified_at' => now(),
            ]);
        }

        $token = $user->createToken('talemark-mobile')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login Google berhasil',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
        ]);
    }
}
