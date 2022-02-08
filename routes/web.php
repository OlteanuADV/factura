<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Pages;
use App\Http\Controllers\API;
use App\Http\Controllers\ANAF;
use App\Http\Controllers\SEAP;


Route::middleware('OnlyJSON')->prefix('/api')->group(function() {
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
        Route::get('/select/{id}', [API::class, 'companySelect']);
    });

    Route::get('/checkLoginGoogle/{token}', [API::class, 'checkLoginGoogle'])->middleware('guest');
});

Route::get('/logout', function() {
    Auth::logout();
    return redirect('/');
});

Route::get('/{any}', [Pages::class, 'home'])->where('any', '^(?!api).*$')->where('any', '^(?!logout).*$')->name('home');
