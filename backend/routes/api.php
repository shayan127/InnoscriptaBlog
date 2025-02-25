<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\MetadataController;
use App\Http\Middleware\AuthenticateApi;
use Illuminate\Support\Facades\Route;

Route::middleware(AuthenticateApi::class)->group(function() {
    Route::get('/sources', [MetadataController::class, 'sources']);
    Route::get('/authors', [MetadataController::class, 'authors']);

    // category routes
    Route::get('/categories', [MetadataController::class, 'categories']);
    Route::post('/categories', [MetadataController::class, 'createCategory']);
    Route::put('/categories/{id}', [MetadataController::class, 'updateCategory']);
    Route::delete('/categories/{id}', [MetadataController::class, 'deleteCategory']);

    // blog routes
    Route::post('/blogs', [BlogController::class, 'store']);
    Route::put('/blogs/{id}', [BlogController::class, 'update']);
    Route::get('/blogs/search', [BlogController::class, 'search']);
    Route::delete('/blogs/{id}', [BlogController::class, 'delete']);

    // user routes
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/me', [UserController::class, 'me']);
    Route::post('/refresh', [UserController::class, 'refresh']);
    Route::get('/user-preferences', [UserController::class, 'getUserPreferences']);
    Route::post('/user-preferences', [UserController::class, 'storeUserPreference']);
    Route::delete('/user-preferences/{id}', [UserController::class, 'deleteUserPreference']);
});

Route::post('/login', [UserController::class, 'login']);
