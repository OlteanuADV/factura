<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Pages;
use App\Http\Controllers\API;
use App\Http\Controllers\ANAF;


Route::middleware('OnlyJSON')->prefix('/api')->group(function() {
    Route::get('/', [API::class, 'index']);
    Route::prefix('/anaf')->group(function() {
        Route::get('/search/{cui}', [ANAF::class, 'search']);
        Route::get('/search/advanced/{cui}', [ANAF::class, 'search_advanced']);
    });

    Route::post('/login', [API::class, 'login'])->middleware('guest');
});

Route::get('/{any}', [Pages::class, 'home'])->where('any', '^(?!api).*$')->name('home');
