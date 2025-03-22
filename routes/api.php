<?php

use App\Http\Controllers\API\TagController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
//
//Route::prefix('tags')->middleware('auth:sanctum')->group(function () {
//    Route::get('/', [TagController::class, 'index']);
//});
