<?php
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () { return view('index'); });

Route::get('/login_user', function () { return view('login_user');})->name('login_user');

Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');

Route::get('/setting', function () { return view('setting'); })->name('setting');

// tampil form login
Route::get('/login', function () { return view('login'); })->name('login');

// proses login
Route::post('/login', [AuthController::class, 'login']);

// dashboard (setelah login)
Route::get('/dashboard', function () { return view('dashboard');})->middleware('auth')->name('dashboard');
