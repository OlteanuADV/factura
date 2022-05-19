<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Pages;
use App\Http\Controllers\API;
use App\Http\Controllers\ANAF;
use App\Http\Controllers\SEAP;


Route::prefix('/api')->group(function() { // middleware('OnlyJSON')->
    Route::get('/', [API::class, 'index']);

    Route::prefix('/anaf')->group(function() {
        Route::get('/search/{cui}', [ANAF::class, 'search']);
        Route::get('/search/advanced/{cui}', [ANAF::class, 'search_advanced']);
    });

    Route::prefix('/seap')->group(function() {
        Route::get('/search', [SEAP::class, 'search']);
        Route::get('/cpvs', [SEAP::class, 'getCpvs']);
        Route::get('/cpvs/my', [SEAP::class, 'getMyCpvs']);
        Route::post('/cpvs/add', [SEAP::class, 'addCpv']);
        Route::get('/words/my', [SEAP::class, 'getMyWords']);
        Route::post('/words/add', [SEAP::class, 'addWord']);
        Route::get('/announces/my', [SEAP::class, 'getMyAnnounces']);
    });

    Route::prefix('/company')->group(function() {
        Route::post('/create', [API::class, 'companyCreate']);
        Route::get('/select/{id}', [API::class, 'companySelect']);
    });

    Route::prefix('/client')->group(function() {
        Route::post('/search', [API::class, 'clientSearch']);
        Route::post('/add', [API::class, 'clientAdd']);
        Route::post('/anaf', [API::class, 'clientAnaf']);
    });

    Route::prefix('/login')->group(function() {
        Route::get('/google/{token}', [API::class, 'checkLoginGoogle'])->middleware('guest');
        Route::get('/facebook/{token}', [API::class, 'checkLoginFacebook'])->middleware('guest');
    });
});

Route::get('/logout', function() {
    Auth::logout();
    return redirect('/');
});

Route::get('/{any}', [Pages::class, 'home'])->where('any', '^(?!api).*$')->where('any', '^(?!logout).*$')->name('home');
