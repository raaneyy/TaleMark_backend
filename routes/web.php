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
