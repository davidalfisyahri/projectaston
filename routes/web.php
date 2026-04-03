<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/login_user', function () {
    return view('login_user');
})->name('login_user');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');