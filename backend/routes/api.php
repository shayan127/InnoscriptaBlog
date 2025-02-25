<?php

use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\MetadataController;
use Illuminate\Routing\Route;


Route::get('/sources', [MetadataController::class, 'sources']);
Route::get('/categories', [MetadataController::class, 'categories']);
Route::get('/authors', [MetadataController::class, 'authors']);

Route::post('/categories', [MetadataController::class, 'createCategory']);
Route::put('/categories/{id}', [MetadataController::class, 'updateCategory']);
Route::delete('/categories/{id}', [MetadataController::class, 'deleteCategory']);

Route::post('/blogs', [BlogController::class, 'store']);
Route::get('/blogs/search', [BlogController::class, 'search']);
