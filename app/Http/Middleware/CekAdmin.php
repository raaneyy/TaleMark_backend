<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CekAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $adminId = $request->session()->get('admin_id');

        if (!$adminId) {
            return redirect()->route('admin.login')
                ->with('error', 'Silakan masuk terlebih dahulu.');
        }

        $admin = DB::table('admin')->where('id_admin', $adminId)->first();

        if (!$admin) {
            $request->session()->forget('admin_id');

            return redirect()->route('admin.login')
                ->with('error', 'Sesi tidak valid, silakan masuk kembali.');
        }

        // Data admin bisa dipakai di semua view: $adminLogin
        view()->share('adminLogin', $admin);

        return $next($request);
    }
}