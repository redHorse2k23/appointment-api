<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/profile', [AuthController::class, 'profile']);

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::middleware('role:admin')->group(function () {

        Route::get('/admin-only', function () {
            return response()->json([
                'message' => 'Admin only route'
            ]);
        });

    });

    Route::middleware('role:manager,admin')->group(function () {

        Route::get('/manager-only', function () {
            return response()->json([
                'message' => 'Manager or Admin route'
            ]);
        });

    });

    Route::middleware('role:user')->group(function () {

        Route::get('/user-only', function () {
            return response()->json([
                'message' => 'User only route'
            ]);
        });

    });

});