<?php

use AmrShawky\LaravelCurrency\Facade\Currency;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InputKodeController;

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
    return view('app');
})->where('any', '.*');
