<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    logger('Testing Telescope Log Entry');
    return "Test Route Working";
});

Route::get('/error', function () {
    abort(500);
});