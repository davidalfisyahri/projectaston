<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\CustomerRequestController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\GradebetonController;
use App\Http\Controllers\ProcurementController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\DashboardController;
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

Route::post('/users', [UsersController::class, 'store']);
Route::post('/user/update/{id}', [UsersController::class, 'update']);
Route::delete('/users/{id}', [UsersController::class, 'destroy']);

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/customer_req', [CustomerRequestController::class, 'index'])->name('customer_req');
Route::get('/customer-request', [CustomerRequestController::class, 'index']);
Route::post('/customer-request/store', [CustomerRequestController::class, 'store']);
Route::delete('/customer-request/delete/{id}', [CustomerRequestController::class, 'destroy']);
Route::get('/customer-request/pdf/{id}', [CustomerRequestController::class, 'pdf']);
Route::get('/customer-request/spk-pdf/{id}', [CustomerRequestController::class, 'spkPdf'])->name('customer-request.spk-pdf');
Route::get('/customer-request/invoice-pdf/{id}', [CustomerRequestController::class, 'invoicePdf'])->name('customer-request.invoice-pdf');


Route::post('/customer-request/approve/{id}', [CustomerRequestController::class, 'approve']);
Route::post('/customer-request/pay/{id}', [CustomerRequestController::class, 'pay']);
Route::post('/customer-request/confirm-wa/{id}', [CustomerRequestController::class, 'confirmWa']);
Route::post('/customer-request/schedule/{id}', [CustomerRequestController::class, 'schedule']);
Route::post('/customer-request/done/{id}', [CustomerRequestController::class, 'markAsDone']);

Route::get('/plant-schedule', [CustomerRequestController::class, 'plantSchedule'])->middleware('auth')->name('plant_schedule');

Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
Route::post('/inventory/store', [InventoryController::class, 'store']);
Route::post('/inventory/update/{id}', [InventoryController::class, 'update']);
Route::get('/inventory/delete/{id}', [InventoryController::class, 'destroy']);

Route::post('/grade/store', [GradebetonController::class, 'store']);
Route::post('/grade/update/{id}', [GradebetonController::class, 'update']);
Route::get('/grade/delete/{id}', [GradebetonController::class, 'destroy']);
Route::post('/grade/bulk-store', [GradebetonController::class, 'bulkStore']);

Route::get('/procurement', [ProcurementController::class, 'index']) ->name('procurement');
Route::post('/procurement/store', [ProcurementController::class, 'store']);
Route::post('/procurement/store-pdf', [ProcurementController::class, 'storePdf']);
Route::get('/procurement/pdf/{id}', [ProcurementController::class, 'pdf']);
Route::delete('/procurement/delete/{id}', [ProcurementController::class, 'delete']);

Route::get('/setting', [UsersController::class, 'index'])->middleware('auth')->name('setting');

// tampil form login
Route::get('/login', function () { return view('login'); })->name('login');
Route::post('/login', [AuthController::class, 'login']);

// dashboard (setelah login)
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

Route::get('/', function () { return view('index'); });

// STOCK OPNAME
Route::get('/stock-opname', [StockOpnameController::class, 'index'])->middleware('auth')->name('stock_opname');
Route::post('/stock-opname', [StockOpnameController::class, 'store'])->middleware('auth');

// APPROVAL (hanya direktur & wakil direktur)
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\DeliveryTariffController;

Route::middleware(['auth', 'isDirector'])->group(function () {
    Route::get('/approval', [ApprovalController::class, 'index'])->name('approval');
    Route::post('/approval/customer-request/{id}', [ApprovalController::class, 'approveCustomerRequest'])->name('approval.customer_request');
    Route::post('/approval/procurement/{id}', [ApprovalController::class, 'approveProcurement'])->name('approval.procurement');
});

// DELIVERY TARIFF (superadmin, direktur_utama, wakil_direktur)
Route::middleware(['auth'])->group(function () {
    Route::post('/setting/delivery-tariffs', [DeliveryTariffController::class, 'update'])->name('delivery-tariffs.update');
    Route::post('/setting/delivery-tariffs/store', [DeliveryTariffController::class, 'store'])->name('delivery-tariffs.store');
    Route::delete('/setting/delivery-tariffs/{id}', [DeliveryTariffController::class, 'destroy'])->name('delivery-tariffs.destroy');
});

// API: tarif pengiriman (untuk JavaScript di form order)
Route::get('/api/delivery-tariffs', [DeliveryTariffController::class, 'getTariffs']);

// API: resolve Google Maps short URL to coordinates
Route::post('/api/resolve-maps-url', [CustomerRequestController::class, 'resolveMapsUrl']);

// =====================
// CUSTOMER PORTAL
// =====================
use App\Http\Controllers\CustomerPortalController;

Route::get('/customer/register', [CustomerPortalController::class, 'showRegister'])->name('customer.register');
Route::post('/customer/register', [CustomerPortalController::class, 'register']);

Route::middleware(['auth', 'isCustomer'])->prefix('customer')->group(function () {
    Route::get('/dashboard', [CustomerPortalController::class, 'dashboard'])->name('customer.dashboard');
    Route::get('/history', [CustomerPortalController::class, 'history'])->name('customer.history');
    Route::get('/orders/status', [CustomerPortalController::class, 'getActiveStatuses'])->name('customer.orders.status');
    Route::post('/order', [CustomerPortalController::class, 'storeOrder'])->name('customer.order.store');
    Route::post('/order/{id}/pay', [CustomerPortalController::class, 'uploadReceipt'])->name('customer.order.pay');
});
