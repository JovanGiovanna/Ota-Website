<?php

use App\Http\Controllers\Categorycontroller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\kamarController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FasilitasController;
use App\Http\Controllers\BookingsController;
use App\Http\Controllers\AddonController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PackageController;
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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', function (Request $request) {
        return response()->json($request->user());
    });
});

Route::apiResource('kamars', kamarController::class);
Route::post('/kamars/store', [kamarController::class, 'store']);
Route::apiResource('fasilitas', FasilitasController::class);
Route::post('/fasilitas/store', [FasilitasController::class, 'store']);
Route::apiResource('category', Categorycontroller::class);
Route::apiResource('/addons', AddonController::class);
Route::get('/bookings', [BookingsController::class, 'index']);

// New API Routes for Province, City, Product, Package
Route::apiResource('provinces', ProvinceController::class);
Route::get('/provinces/search', [ProvinceController::class, 'searchApi']);
Route::get('/provinces/welcome', [ProvinceController::class, 'welcome']);
Route::apiResource('cities', CityController::class);
Route::get('/cities/search', [CityController::class, 'searchApi']);
Route::apiResource('products', ProductController::class);
Route::get('/products/search', [ProductController::class, 'searchApi']);
Route::apiResource('packages', PackageController::class);
Route::get('/packages/search', [PackageController::class, 'searchApi']);

route::post('/checkout/{id}', [BookingsController::class, 'checkout'])->name('booking.checkout');
route::post('/checkin/{id}', [BookingsController::class, 'checkin'])->name('booking.checkin');
Route::prefix('booking')->middleware('auth:sanctum')->group(function () {
route::post('/store', [bookingsController::class, 'store'])->name('booking.store');
Route::post('/{id}/cancel', [BookingsController::class, 'cancelBooking']);
Route::post('/fasilitas', [BookingsController::class, 'storefasilitas']);
  
});
