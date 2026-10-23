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
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $admin = DB::table('admin')
            ->where('username', $credentials['username'])
            ->first();

        if (
            !$admin ||
            !Hash::check($credentials['password'], $admin->password)
        ) {
            return back()
                ->withInput($request->only('username'))
                ->with('error', 'Username atau kata sandi salah.');
        }

        $request->session()->regenerate();

        $request->session()->put('admin_id', $admin->id_admin);

        return redirect()->route('admin.dashboard');
    }
}
