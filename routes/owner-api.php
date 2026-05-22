<?php

use Illuminate\Support\Facades\Route;


Route::post('/create-court', "CourtController@createCourt");
Route::get('/all-courts', "CourtController@allCourt");




Route::get('/owner-only', function () {
    return response()->json([
        'message' => 'Owner only route'
    ]);
});


 