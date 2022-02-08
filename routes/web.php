<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Pages;
use App\Http\Controllers\API;
use App\Http\Controllers\ANAF;
use App\Http\Controllers\SEAP;


Route::prefix('/api')->group(function() { //middleware('OnlyJSON')->
    Route::get('/', [API::class, 'index']);

    Route::prefix('/anaf')->group(function() {
        Route::get('/search/{cui}', [ANAF::class, 'search']);
        Route::get('/search/advanced/{cui}', [ANAF::class, 'search_advanced']);
    });

    Route::prefix('/seap')->group(function() {
        Route::get('/search', [SEAP::class, 'search']);
    });

    Route::prefix('/company')->group(function() {
        Route::post('/create', [API::class, 'companyCreate']);
    });
    
    Route::post('/login', [API::class, 'login']);
});

Route::get('/{any}', [Pages::class, 'home'])->where('any', '^(?!api).*$')->name('home');
