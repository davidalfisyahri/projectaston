<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;

Route::get('/', function () { return view('index'); });

Route::get('/login_user', function () { return view('login_user');})->name('login_user');
use Illuminate\Support\Facades\Auth;

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login_user');
    })->name('logout');

Route::get('/setting', function () {
return view('setting');
})->middleware('auth', 'isSuperAdmin')->name('setting');

Route::post('/users', [UsersController::class, 'store']);
Route::put('/users/{id}', [UsersController::class, 'update']);
Route::delete('/users/{id}', [UsersController::class, 'destroy']);

Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');

Route::get('/setting', function () { return view('setting'); })->name('setting');
Route::get('/setting', [UsersController::class, 'index'])->name('setting');

// tampil form login
Route::get('/login', function () { return view('login'); })->name('login');
Route::post('/login', [AuthController::class, 'login']);

// dashboard (setelah login)
Route::get('/dashboard', function () { return view('dashboard');})->middleware('auth')->name('dashboard');

Route::get('/', function () { return view('index'); });

// DASHBOARD
