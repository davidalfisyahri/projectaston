<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\CustomerRequestController;
use App\Http\Controllers\inventoryController;
use App\Http\Controllers\gradebetonController;
use App\Http\Controllers\procurementcontroller;
use App\Http\Controllers\StockOpnameController;
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
Route::post('/user/update/{id}', [UsersController::class, 'update']);
Route::delete('/users/{id}', [UsersController::class, 'destroy']);

Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');

Route::get('/customer_req', [CustomerRequestController::class, 'index'])->name('customer_req');
Route::get('/customer-request', [CustomerRequestController::class, 'index']);
Route::post('/customer-request/store', [CustomerRequestController::class, 'store']);
Route::delete('/customer-request/delete/{id}', [CustomerRequestController::class, 'destroy']);
Route::get('/customer-request/pdf/{id}', [CustomerRequestController::class, 'pdf']);

Route::post('/customer-request/approve/{id}', [CustomerRequestController::class, 'approve']);
Route::post('/customer-request/pay/{id}', [CustomerRequestController::class, 'pay']);
Route::post('/customer-request/confirm-wa/{id}', [CustomerRequestController::class, 'confirmWa']);
Route::post('/customer-request/schedule/{id}', [CustomerRequestController::class, 'schedule']);
Route::get('/customer-request/pdf/{id}', [CustomerRequestController::class, 'pdf']);

Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
Route::post('/inventory/store', [InventoryController::class, 'store']);
Route::post('/inventory/update/{id}', [InventoryController::class, 'update']);
Route::get('/inventory/delete/{id}', [InventoryController::class, 'destroy']);

Route::post('/grade/store', [gradebetonController::class, 'store']);
Route::post('/grade/update/{id}', [gradebetonController::class, 'update']);
Route::get('/grade/delete/{id}', [gradebetonController::class, 'destroy']);

Route::get('/procurement', [procurementcontroller::class, 'index']) ->name('procurement');
Route::post('/procurement/store', [procurementcontroller::class, 'store']);
Route::post('/procurement/store-pdf', [procurementcontroller::class, 'storePdf']);
Route::get('/procurement/pdf/{id}', [procurementcontroller::class, 'pdf']);
Route::delete('/procurement/delete/{id}', [procurementcontroller::class, 'delete']);

Route::get('/setting', function () { return view('setting'); })->name('setting');
Route::get('/setting', [UsersController::class, 'index'])->name('setting');

// tampil form login
Route::get('/login', function () { return view('login'); })->name('login');
Route::post('/login', [AuthController::class, 'login']);

// dashboard (setelah login)
Route::get('/dashboard', function () { return view('dashboard');})->middleware('auth')->name('dashboard');

Route::get('/', function () { return view('index'); });

// STOCK OPNAME
Route::get('/stock-opname', [StockOpnameController::class, 'index'])->middleware('auth')->name('stock_opname');
Route::post('/stock-opname', [StockOpnameController::class, 'store'])->middleware('auth');
