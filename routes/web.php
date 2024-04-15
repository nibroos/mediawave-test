<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect(env('FRONTEND_URL'));
});

Route::get('/home', function () {
    return view('welcome');
});
