<?php

use Illuminate\Support\Facades\Route;



Route::post('/create-user', "AdminController@createAccount");


Route::get('/admin-only', function () {
    return response()->json([
        'message' => 'Admin only route'
    ]);
});

