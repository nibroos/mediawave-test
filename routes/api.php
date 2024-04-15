<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// HANYA BOLEH MENGGUNAKAN METHOD POST

Route::post('test', function () {
    return response()->json(['message' => 'Hello World!']);
});

Route::post('/auth/login', [AuthController::class, 'loginUser']);
Route::post('/auth/register', [AuthController::class, 'registerUser']);

Route::group(
    ['middleware' => 'auth:sanctum'],
    function () {
        Route::post('/auth/logout', [AuthController::class, 'logoutUser']);
        Route::post('/auth/user', [AuthController::class, 'getAuthUser']);
    }
);
