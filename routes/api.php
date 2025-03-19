<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::post('/register', 'register');
    Route::post('/logout', 'logout');
    Route::post('/login', 'login');
    Route::post('/verify-token', 'verifyToken')->middleware('auth:sanctum');
    Route::post('/refresh', 'refreshToken')->middleware('auth:sanctum');
});
