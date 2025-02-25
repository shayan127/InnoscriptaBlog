<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\MetadataController;
use App\Http\Middleware\AuthenticateApi;
use Illuminate\Support\Facades\Route;

Route::middleware(AuthenticateApi::class)->group(function() {

Route::get('/sources', [MetadataController::class, 'sources']);
Route::get('/categories', [MetadataController::class, 'categories']);
Route::get('/authors', [MetadataController::class, 'authors']);

Route::post('/categories', [MetadataController::class, 'createCategory']);
Route::put('/categories/{id}', [MetadataController::class, 'updateCategory']);
Route::delete('/categories/{id}', [MetadataController::class, 'deleteCategory']);

Route::post('/blogs', [BlogController::class, 'store']);
Route::get('/blogs/search', [BlogController::class, 'search']);


    // user routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});

Route::post('/login', [AuthController::class, 'login']);
