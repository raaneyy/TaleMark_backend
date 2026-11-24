<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\QuoteApiController;
use App\Http\Controllers\Api\ReadingSessionApiController;
use App\Http\Controllers\Api\WeeklyRecapApiController;
use App\Http\Controllers\Api\BookScanApiController;

Route::get('/hello', function () {
    return response()->json([
        'success' => true,
        'message' => 'Laravel berhasil terhubung dengan Flutter!'
    ]);
});

// Autentikasi pengguna
Route::post('/register', [AuthApiController::class, 'register']);
Route::post('/login', [AuthApiController::class, 'login']);
Route::post('/login/google', [AuthApiController::class, 'loginWithGoogle']);

// ==========================================
// Fitur Prioritas 3 Menengah (Calvin)
// ==========================================

// 1. F016 - Kutipan Favorit
Route::get('/quotes', [QuoteApiController::class, 'index']);
Route::post('/quotes', [QuoteApiController::class, 'store']);
Route::put('/quotes/{id}/favorite', [QuoteApiController::class, 'toggleFavorite']);
Route::delete('/quotes/{id}', [QuoteApiController::class, 'destroy']);

// 2. F014 - Timer Membaca (Sesi Baca)
Route::get('/reading-sessions', [ReadingSessionApiController::class, 'index']);
Route::post('/reading-sessions', [ReadingSessionApiController::class, 'store']);

// 3. F013 - Rekap Mingguan
Route::get('/stats/weekly-recap', [WeeklyRecapApiController::class, 'index']);

// 4. F006 - Scan Barcode/ISBN
Route::get('/books/scan/{isbn}', [BookScanApiController::class, 'scan']);
Route::post('/books/scan/save', [BookScanApiController::class, 'save']);

// Endpoint yang membutuhkan token Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'user' => $request->user(),
        ]);
    });
});
