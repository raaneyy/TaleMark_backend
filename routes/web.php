<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/*
 * TODO: Route sementara untuk melihat tampilan login admin.
 * Ganti route ini dengan controller dan middleware autentikasi dari backend.
 */
Route::get('/admin/login', fn () => view('admin.login'));

/*
 * Route sementara untuk halaman admin dashboard (tampilan saja).
 * Nanti akan diganti oleh backend dengan middleware auth dan controller yang sesuai.
 * Contoh: Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->middleware('auth');
 */
Route::get('/admin/dashboard', fn () => view('admin.dashboard'))->name('admin.dashboard');

/*
 * Route sementara untuk melihat tampilan Validasi & Kurasi Buku Manual (tampilan saja).
 * Nanti akan diganti oleh tim backend dengan middleware auth dan controller yang sesuai.
 */
Route::get('/admin/validasi-buku', fn () => view('admin.validasi-buku'))->name('admin.validasi-buku');
