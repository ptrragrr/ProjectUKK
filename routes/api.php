<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\RegisterController;
// use App\Http\Controllers\KonserController;
use App\Http\Controllers\InputKodeController;
use App\Http\Controllers\pengguna\CheckoutController;
use App\Http\Controllers\pengguna\PaymentController;
// use App\Http\Controllers\TicketTransaksiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');

// Authentication Route
Route::middleware(['auth', 'json'])->prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->withoutMiddleware('auth');
    Route::delete('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
});

Route::post('auth/register', [RegisterController::class, 'register'])->withoutMiddleware('auth');

Route::prefix('setting')->group(function () {
    Route::get('', [SettingController::class, 'index']);
});

Route::middleware('auth')->group(function () {
    Route::get('/me', [UserController::class, 'me']);         // ambil profile user login
    Route::put('/me', [UserController::class, 'updateMe']);   // update profile user login
});

Route::middleware(['auth', 'verified', 'json'])->group(function () {
    Route::prefix('setting')->middleware('can:setting')->group(function () {
        Route::post('', [SettingController::class, 'update']);
    });

    Route::prefix('master')->group(function () {
        Route::middleware('can:master-user')->group(function () {
            Route::get('/users', [UserController::class, 'index']);       // list user (paginate)
            Route::get('/users/all', [UserController::class, 'get']);     // ambil semua user
            Route::post('/users/store', [UserController::class, 'store']);      // tambah user
            Route::put('/users/{user}', [UserController::class, 'update']); 
            Route::get('/master/users/{uuid}', [UserController::class, 'show']);  // update user by admin
            Route::delete('/users/{user}', [UserController::class, 'destroy']); // hapus user
            Route::get('/roles', [UserController::class, 'getRoles']);
            Route::put('/profile', [UserController::class, 'updateMe']);

        });

        Route::middleware('can:master-role')->group(function () {
    // ✅ Untuk pagination dan search
    Route::get('roles', [RoleController::class, 'index']);

    // ✅ Untuk tambah role baru
    Route::post('roles/store', [RoleController::class, 'store']);

    // ✅ CRUD lainnya
    Route::apiResource('roles', RoleController::class)
        ->except(['index', 'store']);
});
    });
});

Route::get('/tickets-transaksi', [TransaksiController::class, 'index']);

Route::get('/tickets/get', [TicketController::class, 'get']);
Route::get('/tickets', [TicketController::class, 'index']);
Route::post('/tickets', [TicketController::class, 'store']);
// Route::get('/tickets/{id}', [TicketController::class, 'show']);
Route::get('/tickets/{ticket}', [TicketController::class, 'show']);
Route::put('/tickets/{id}', [TicketController::class, 'update']);
Route::patch('/tickets/{id}', [TicketController::class, 'update']);
Route::delete('/tickets/{id}', [TicketController::class, 'destroy']);

Route::get('/transaksi', [TransaksiController::class, 'index']);          // 🔍 List semua konser
Route::post('/transaksi/store', [TransaksiController::class, 'store']);         // ➕ Tambah konser baru
Route::get('/transaksi/{id}', [TransaksiController::class, 'show']);      // 👁 Detail konser
Route::put('/transaksi/{id}', [TransaksiController::class, 'update']);    // ✏️ Update konser
Route::patch('/transaksi/{id}', [TransaksiController::class, 'update']);  // ✏️ Update parsial konser
Route::delete('/transaksi/{id}', [TransaksiController::class, 'destroy']); // 🗑 Hapus konser

// Route::post('/checkout', [CheckoutController::class, 'index'])->name('checkout.page');
Route::post('/checkout', [CheckoutController::class, 'store']);
Route::post('/checkout/pay', [CheckoutController::class, 'pay'])->name('checkout.pay');
Route::post('/checkout/callback', [CheckoutController::class, 'callback'])->name('checkout.callback');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/failed', [CheckoutController::class, 'failed'])->name('checkout.failed');
Route::get('/checkout/check-status/{orderId}', [CheckoutController::class, 'checkStatus'])->name('checkout.checkStatus');

Route::post('/pay', [CheckoutController::class, 'pay'])->name('checkout.pay');
Route::post('/midtrans/callback', [CheckoutController::class, 'callback'])->name('midtrans.callback');

Route::post('/payment/create', [PaymentController::class, 'createTransaction']);
Route::post('/midtrans/notification', [PaymentController::class, 'notificationHandler']);
Route::post('/transaksi/update-status', [CheckoutController::class, 'updateStatus'])->name('transaksi.updateStatus');
Route::get('/tiket/{id}', [CheckoutController::class, 'showTiket'])
    ->name('tiket.show');

Route::get('/cek-tiket/{kode}', [InputKodeController::class, 'cek']);

