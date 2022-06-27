<?php

use App\Http\Controllers\ClosureController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Shopper\ShopperQueueController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ClosureController::class, 'index']);

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::middleware(['auth:sanctum', 'verified'])
    ->namespace('Store')
    ->prefix('store')
    ->name('store.')
    ->group(__DIR__ . '/Store/web.php');

Route::namespace('Store')
    ->prefix('sign-in')
    ->name('public.')
    ->group(__DIR__ . '/Store/Location/public.php');


Route::middleware('auth:shoppers')->group(function () {
	Route::get('shopper/{shopper}', [ShopperQueueController::class, 'dashboard'])->name('shopper.dashboard');
	Route::get('shopper/check_out/{shopper}/{location}', [ShopperQueueController::class, 'check_out'])->name('shopper.check_out');
});

