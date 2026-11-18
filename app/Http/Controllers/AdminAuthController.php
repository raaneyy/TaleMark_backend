<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $admin = DB::table('admin')
            ->where('email', $credentials['email'])
            ->first();

        if (
            !$admin ||
            !Hash::check($credentials['password'], $admin->password)
        ) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Email atau kata sandi salah.');
        }

        $request->session()->regenerate();

        $request->session()->put('admin_id', $admin->id_admin);

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        // Hapus data session admin
        $request->session()->forget('admin_id');
        
        // Invalidate session & regenerate CSRF token demi keamanan
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect kembali ke halaman login (sesuaikan nama route jika berbeda)
        return redirect()->route('admin.login')->with('success', 'Berhasil keluar.');
    }
}