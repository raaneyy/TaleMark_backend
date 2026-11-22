
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;

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
