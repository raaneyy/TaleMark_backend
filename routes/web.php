<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/*
 * Route sementara untuk halaman admin dashboard (tampilan saja).
 * Nanti akan diganti oleh backend dengan middleware auth dan controller yang sesuai.
 * Contoh: Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->middleware('auth');
 */
Route::get('/admin/dashboard', fn () => view('admin.dashboard'))->name('admin.dashboard');
