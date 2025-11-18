<?php

use AmrShawky\LaravelCurrency\Facade\Currency;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InputKodeController;
use App\Http\Controllers\pengguna\CheckoutController;

Route::get('/checkout/callback', [CheckoutController::class, 'callback'])->name('checkout.callback');
Route::get('/checkout/success', [CheckoutController::class, 'success']);
Route::get('/checkout/failed', [CheckoutController::class, 'failed'])->name('checkout.failed');
// Route untuk halaman pending
Route::get('/checkout/pending', function () {
    $orderId = request()->query('order_id');
    return view('checkout.pending', compact('orderId'));
})->name('checkout.pending');
// Route::get('/checkout/pending', [CheckoutController::class, 'pending'])->name('checkout.pending');

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('app');
// });

// Route::get('/cek-tiket/{kode}', [InputKodeController::class, 'cek']);
Route::get('/cetak-tiket/{kode}', [InputKodeController::class, 'cetak'])->name('cetak.tiket');
Route::post('/selesai/{kode}', [InputKodeController::class, 'selesai']);

Route::get('/{any}', function () {
   return view('layouts.app');
})->where('any', '.*');

Route::get('/dashboard_pengguna/{any}', function () {
    return view('dashboard_pengguna'); // atau view utama Vue kamu
})->where('any', '.*');

Route::get('/checkout/success', function () {
    return view('checkout.success');
})->name('checkout.success');

Route::get('/checkout/failed', function () {
    return view('checkout.failed');
})->name('checkout.failed');
