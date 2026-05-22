<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::get('/user-only', function () {
    return response()->json([
        'message' => 'User only route'
    ]);
});
