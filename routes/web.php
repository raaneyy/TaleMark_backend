<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminProfilController;

Route::get('/', function () {
    return view('welcome');
});

// Halaman login admin (boleh dibuka tanpa login)
Route::get('/admin/login', fn () => view('admin.login'))
    ->name('admin.login');

// Proses login admin
Route::post('/admin/login', [AdminAuthController::class, 'login'])
    ->name('admin.login.post');

// =============================================
// Semua halaman admin: WAJIB LOGIN
// (nama & foto admin otomatis tampil di topbar)
// =============================================
Route::middleware(\App\Http\Middleware\CekAdmin::class)->prefix('admin')->group(function () {

    Route::get('/dashboard', fn () => view('admin.dashboard'))
        ->name('admin.dashboard');

    Route::get('/validasi-buku', fn () => view('admin.validasi-buku'))
        ->name('admin.validasi-buku');

    Route::get('/laporan-akun', fn () => view('admin.laporan-akun'))
        ->name('admin.laporan-akun');

    Route::get('/siaran-notifikasi', fn () => view('admin.siaran-notifikasi'))
        ->name('admin.siaran-notifikasi');

    // Setting Akun
    Route::get('/profil', [AdminProfilController::class, 'show'])->name('admin.profil');
    Route::put('/profil', [AdminProfilController::class, 'update'])->name('admin.profil.update');

    // Logout
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});