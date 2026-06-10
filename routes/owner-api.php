<?php

use Illuminate\Support\Facades\Route;


Route::post('/create-court', "CourtController@createCourt");
Route::get('/all-courts', "CourtController@allCourt");
Route::get('/show-court/{courtId}',"CourtController@showCourt");


Route::post('/create-schedule/{courtId}', "CourtController@createCourtSchedule");
Route::get('/list-schedules/{courtId}',"CourtController@getCourtSchedules");

Route::post('/court/{courtId}/upload-image', "CourtController@uploadAttachment");
Route::delete('/court/{courtId}/attachment/{attachmentId}', "CourtController@deleteAttachment");
Route::get('/court/{courtId}/attachments', "CourtController@getAttachments");