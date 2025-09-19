<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\KonserController;
use App\Http\Controllers\InputKodeController;
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

Route::middleware(['auth', 'verified', 'json'])->group(function () {
    Route::prefix('setting')->middleware('can:setting')->group(function () {
        Route::post('', [SettingController::class, 'update']);
    });

    Route::prefix('master')->group(function () {
        Route::middleware('can:master-user')->group(function () {
            Route::get('users', [UserController::class, 'get']);
            Route::post('users', [UserController::class, 'index']);
            Route::post('users/store', [UserController::class, 'store']);
            Route::apiResource('users', UserController::class)
                ->except(['index', 'store'])->scoped(['user' => 'uuid']);
        });

        Route::middleware('can:master-role')->group(function () {
            Route::get('roles', [RoleController::class, 'get'])->withoutMiddleware('can:master-role');
            Route::post('roles', [RoleController::class, 'index']);
            Route::post('roles/store', [RoleController::class, 'store']);
            Route::apiResource('roles', RoleController::class)
                ->except(['index', 'store']);
        });
    });

Route::get('/tickets-transaksi', [TransaksiController::class, 'index']);
});

Route::get('/tickets', [TicketController::class, 'index']);
Route::post('/tickets', [TicketController::class, 'store']);
// Route::get('/tickets/{id}', [TicketController::class, 'show']);
Route::get('/tickets/{ticket}', [TicketController::class, 'show']);
Route::put('/tickets/{id}', [TicketController::class, 'update']);
Route::patch('/tickets/{id}', [TicketController::class, 'update']);
Route::delete('/tickets/{id}', [TicketController::class, 'destroy']);

Route::get('/konser', [KonserController::class, 'index']);          // 🔍 List semua konser
Route::post('/konser', [KonserController::class, 'store']);         // ➕ Tambah konser baru
Route::get('/konser/{id}', [KonserController::class, 'show']);      // 👁 Detail konser
Route::put('/konser/{id}', [KonserController::class, 'update']);    // ✏️ Update konser
// Route::patch('/konser/{id}', [KonserController::class, 'update']);  // ✏️ Update parsial konser
Route::delete('/konser/{id}', [KonserController::class, 'destroy']); // 🗑 Hapus konser

Route::get('/transaksi', [TransaksiController::class, 'index']);          // 🔍 List semua konser
Route::post('/transaksi/store', [TransaksiController::class, 'store']);         // ➕ Tambah konser baru
Route::get('/transaksi/{id}', [TransaksiController::class, 'show']);      // 👁 Detail konser
Route::put('/transaksi/{id}', [TransaksiController::class, 'update']);    // ✏️ Update konser
Route::patch('/transaksi/{id}', [TransaksiController::class, 'update']);  // ✏️ Update parsial konser
Route::delete('/transaksi/{id}', [TransaksiController::class, 'destroy']); // 🗑 Hapus konser
// Route::post('/transaksi/{id}/kode-tiket', [TransaksiController::class, 'simpanKodeTiket']);
// Route::get('/transaksi/{id}/cetak', [TransaksiController::class, 'cetakTiket']);

// Route::post('/input-tiket', [InputTiketController::class, 'simpan']);
Route::get('/cek-tiket/{kode}', [InputKodeController::class, 'cek']);
// Route::get('/cetak-tiket/{kode}', [InputKodeController::class, 'cetak'])->name('cetak.tiket');


// Route::get('/tickettransaksi', [TicketTransaksiController::class, 'index']);          // 🔍 List semua konser
// Route::post('/tickettransaksi', [TicketTransaksiController::class, 'store']);         // ➕ Tambah konser baru
// Route::get('/tickettransaksi/{id}', [TicketTransaksiController::class, 'show']);      // 👁 Detail konser
// Route::put('/tickettransaksi/{id}', [TicketTransaksiController::class, 'update']);    // ✏️ Update konser
// Route::patch('/tickettransaksi/{id}', [TicketTransaksiController::class, 'update']);  // ✏️ Update parsial konser
// Route::delete('/tickettransaksi/{id}', [TicketTransaksiController::class, 'destroy']); // 🗑 Hapus konser

