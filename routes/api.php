<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Container\Attributes\Auth;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\API\Role\RoleController;
use App\Http\Controllers\API\Users\UsersController;
use App\Http\Controllers\API\Permissions\PermissionController;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// role and permission routes

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/permissions', [PermissionController::class, 'index']);
    Route::get('/permissions/{permission}', [PermissionController::class, 'show']);
    Route::post('/permissions', [PermissionController::class, 'store']);
    Route::put('/permissions/{permission}', [PermissionController::class, 'update']);
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/roles_show', [RoleController::class, 'index']);
    Route::get('/roles/{role}', [RoleController::class, 'show']);
    Route::post('/roles', [RoleController::class, 'store']);
    Route::put('/roles_update/{role}', [RoleController::class, 'update']);
    Route::delete('/roles/{role}', [RoleController::class, 'destroy']);
    Route::get('/roles/{role}/permissions', [RoleController::class, 'addPermissionToRole']);
    Route::post('/roles/{role}/permissions', [RoleController::class, 'givePermissionToRole']);
});


Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/users', [UsersController::class, 'index']);
    Route::get('/users/{user}', [UsersController::class, 'show']);
    Route::post('/users_store', [UsersController::class, 'store']);
    Route::put('/users_update/{id}', [UsersController::class, 'update']);
    Route::delete('/users_delete/{id}', [UsersController::class, 'destroy']);
    Route::get('/userSummary', [UsersController::class, 'userSummary']);
    Route::post('block-or-unblock/{id}', [UsersController::class, 'blockOrUnblock']);
});
//end that

Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->middleware('auth:sanctum');
    Route::post('/verify-token', 'verifyToken')->middleware('auth:sanctum');
    Route::post('/refresh', 'refreshToken')->middleware('auth:sanctum');
});
