<?php

use Illuminate\Support\Facades\Route;

Route::get('/bookings', 'BookingController@index');
Route::get('/booking/{booking}', 'BookingController@show');
Route::post('/booking', 'BookingController@store');
Route::post('/booking/{booking}/cancel', 'BookingController@cancel');