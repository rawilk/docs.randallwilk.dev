<?php

use App\Http\Controllers\DocsController;
use Illuminate\Support\Facades\Route;

// Package redirects to their "master" branches
Route::redirect('access', 'access/v1/introduction');

Route::get('{package}/{version}/{doc}', DocsController::class)
    ->where('doc', '.*')
    ->name('doc');

Route::view('/', 'pages.docs')->name('home');
