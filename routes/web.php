<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;
use Laravel\Fortify\Features;

// Main SPA route - serves the Vue app
Route::get('/', function () {
    return View::make('app');
})->name('home');

Route::get('/api-docs', function () {
   return View::make('api-docs');
});

require __DIR__.'/settings.php';
