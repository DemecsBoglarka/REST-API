<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CountyController;
use App\Http\Controllers\CityController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

Route::get('/counties', [CountyController::class, 'index']);
Route::get('/county', [CountyController::class, 'search']);
Route::get('/cities', [CityController::class, 'index']);
Route::get('/city', [CityController::class, 'search']);
Route::get('/cities_filtered', [CityController::class, 'filter']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/counties', [CountyController::class, 'store']);
    Route::put('/counties/{id}', [CountyController::class, 'update']);
    Route::delete('/counties/{id}', [CountyController::class, 'destroy']);

    Route::post('/cities', [CityController::class, 'store']);
    Route::put('/cities/{id}', [CityController::class, 'update']);
    Route::delete('/cities/{id}', [CityController::class, 'destroy']);
});
