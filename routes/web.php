<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminProfilController;
use App\Http\Controllers\Auth\ForgotPasswordController;

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



Route::middleware('guest')->group(function () {
    // Halaman isi email
    Route::get('/lupa-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request');

    // Kirim tautan reset (dibatasi 5x per menit)
    Route::post('/lupa-password', [ForgotPasswordController::class, 'sendResetLink'])
        ->middleware('throttle:5,1')
        ->name('password.email');

    // Halaman isi kata sandi baru (nama route 'password.reset' wajib, dipakai Laravel di email)
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])
        ->name('password.reset');

    // Simpan kata sandi baru
    Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])
        ->name('password.update');
});