<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminProfilController;

Route::get('/', function () {
    return view('welcome');
});

// Halaman login admin
Route::get('/admin/login', fn () => view('admin.login'))
    ->name('admin.login');

// Proses login admin
Route::post('/admin/login', [AdminAuthController::class, 'login'])
    ->name('admin.login.post');

// Halaman admin
Route::get('/admin/dashboard', fn () => view('admin.dashboard'))
    ->name('admin.dashboard');

Route::get('/admin/validasi-buku', fn () => view('admin.validasi-buku'))
    ->name('admin.validasi-buku');

Route::get('/admin/laporan-akun', fn () => view('admin.laporan-akun'))
    ->name('admin.laporan-akun');

Route::get('/admin/siaran-notifikasi', fn () => view('admin.siaran-notifikasi'))
    ->name('admin.siaran-notifikasi');

// Halaman yang wajib login admin
Route::middleware(\App\Http\Middleware\CekAdmin::class)->prefix('admin')->group(function () {
    // Halaman Setting Akun
    Route::get('/profil', [AdminProfilController::class, 'show'])->name('admin.profil');
    Route::put('/profil', [AdminProfilController::class, 'update'])->name('admin.profil.update');

    // Logout
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});