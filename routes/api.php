<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CONTROLLERS
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\JenisTiketController;
use App\Http\Controllers\InputKodeController;
use App\Http\Controllers\pengguna\CheckoutController;
use App\Http\Controllers\pengguna\PaymentController;

/*
|--------------------------------------------------------------------------
| PUBLIC AUTH (TANPA LOGIN)
|--------------------------------------------------------------------------
*/

Route::get('/tickets-transaksi', [TransaksiController::class, 'index']);

Route::get('/tickets/
', [TicketController::class, 'get']);
Route::get('/tickets', [TicketController::class, 'index']);
Route::post('/tickets', [TicketController::class, 'store']);
// Route::get('/tickets/{id}', [TicketController::class, 'show']);
Route::get('/tickets/{ticket}', [TicketController::class, 'show']);
Route::put('/tickets/{id}', [TicketController::class, 'update']);
Route::patch('/tickets/{id}', [TicketController::class, 'update']);
Route::delete('/tickets/{id}', [TicketController::class, 'destroy']);
Route::post('/events/{id}/checkout', [TicketController::class, 'checkout']);
Route::post('/tickets/{id}/pay', [TicketController::class, 'pay']);
Route::post('/register', [AuthController::class, 'register']); // ✅ FIX
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

/*
|--------------------------------------------------------------------------
| SETTING (PUBLIC READ)
|--------------------------------------------------------------------------
*/
Route::get('/setting', [SettingController::class, 'index']);

/*
|--------------------------------------------------------------------------
| AUTHENTICATED (SANCTUM)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'json'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | AUTH USER
    |--------------------------------------------------------------------------
    */
    Route::delete('/auth/logout', [AuthController::class, 'logout'])->withoutMiddleware('auth');
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/me', [AuthController::class, 'updateMe']);

    /*
    |--------------------------------------------------------------------------
    | SETTING (ADMIN)
    |--------------------------------------------------------------------------
    */
    Route::prefix('setting')
        ->middleware('can:setting')
        ->group(function () {
            Route::post('', [SettingController::class, 'update']);
        });

    /*
    |--------------------------------------------------------------------------
    | MASTER DATA
    |--------------------------------------------------------------------------
    */
    Route::prefix('master')->group(function () {

        /*
        | USERS
        */
        // Route::middleware('can:master-user')->group(function () {
        //     Route::get('/users', [AuthController::class, 'index']);
        //     Route::get('/users/all', [AuthController::class, 'get']);
        //     Route::post('/users', [AuthController::class, 'store']);
        //     Route::get('/users/{uuid}', [AuthController::class, 'show']);
        //     Route::put('/users/{user}', [AuthController::class, 'update']);
        //     Route::delete('/users/{user}', [AuthController::class, 'destroy']);
        //     Route::get('/roles', [AuthController::class, 'getRoles']);
        // });
        Route::middleware('can:master-user')->group(function () {
            Route::get('/users', [UserController::class, 'index']);
            Route::get('/users/all', [UserController::class, 'get']);
            Route::post('/users', [UserController::class, 'store']);
            Route::get('/users/{uuid}', [UserController::class, 'show']);
            Route::put('/users/{user}', [UserController::class, 'update']);
            Route::delete('/users/{user}', [UserController::class, 'destroy']);
            Route::get('/roles', [UserController::class, 'getRoles']);
        });
        /*
        | ROLES
        */
        Route::middleware('can:master-role')->group(function () {
            Route::get('/roles', [RoleController::class, 'index']);
            Route::post('/roles', [RoleController::class, 'store']);
            Route::put('/roles/{role}', [RoleController::class, 'update']);
            Route::delete('/roles/{role}', [RoleController::class, 'destroy']);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | TICKETS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | TRANSAKSI
    |--------------------------------------------------------------------------
    */
    Route::apiResource('transaksi', TransaksiController::class);

    /*
    |--------------------------------------------------------------------------
    | CHECKOUT
    |--------------------------------------------------------------------------
    */
    Route::post('/checkout', [CheckoutController::class, 'store']);
    Route::post('/checkout/pay', [CheckoutController::class, 'pay']);
    Route::post('/checkout/release', [CheckoutController::class, 'releaseReservation']);
    Route::get('/checkout/check-status/{orderId}', [CheckoutController::class, 'checkStatus']);
});

/*
|--------------------------------------------------------------------------
| CHECKOUT CALLBACK (PUBLIC – PAYMENT GATEWAY)
|--------------------------------------------------------------------------
*/
Route::post('/checkout/callback', [CheckoutController::class, 'callback']);
Route::get('/checkout/success', [CheckoutController::class, 'success']);
Route::get('/checkout/failed', [CheckoutController::class, 'failed']);

Route::post('/midtrans/callback', [CheckoutController::class, 'callback']);
Route::post('/midtrans/notification', [PaymentController::class, 'notificationHandler']);

/*
|--------------------------------------------------------------------------
| JENIS TIKET
|--------------------------------------------------------------------------
*/
Route::prefix('jenis-tiket')->controller(JenisTiketController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/all', 'all');
    Route::post('/', 'store');
    Route::get('/{id}', 'show');
    Route::put('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
});

/*
|--------------------------------------------------------------------------
| CEK TIKET
|--------------------------------------------------------------------------
*/
Route::get('/cek-tiket/{kode}', [InputKodeController::class, 'cek']);
Route::get('/tiket/{id}', [CheckoutController::class, 'showTiket']);
