<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;

use App\Http\Controllers\Api\V1\ActivityController;

Route::prefix('v1')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);

    Route::post('/login', [AuthController::class, 'login']);

    Route::post('/logout', [AuthController::class, 'logout'])
        ->middleware('auth:sanctum');

    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::post('/activities', [ActivityController::class, 'store'])
        ->middleware('auth:sanctum');

    Route::get('/activities', [ActivityController::class, 'index'])
        ->middleware('auth:sanctum');

    Route::get('/activities/{id}', [ActivityController::class, 'show'])
        ->middleware('auth:sanctum');

    Route::patch('/activities/{id}', [ActivityController::class, 'update'])
        ->middleware('auth:sanctum');

    Route::get('/categories', [CategoryController::class, 'index'])
    ->middleware('auth:sanctum');

    Route::post('/categories', [CategoryController::class, 'store'])
    ->middleware('auth:sanctum');

    Route::patch('/categories/{id}', [CategoryController::class, 'update'])
    ->middleware('auth:sanctum');

    Route::patch('/categories/{id}/status', [CategoryController::class, 'updateStatus'])
    ->middleware('auth:sanctum');

    Route::patch('/activities/{id}/complete', [ActivityController::class, 'complete'])
    ->middleware('auth:sanctum');

    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])
    ->middleware('auth:sanctum');

});