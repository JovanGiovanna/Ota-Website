<?php

use App\Http\Controllers\Categorycontroller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\kamarController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VendorAuthController; // <<< Import Controller Vendor
use App\Http\Controllers\FasilitasController;
use App\Http\Controllers\BookingsController;
use App\Http\Controllers\AddonController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ProductController;

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

// --- PENGGUNA (USER) API AUTH ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', function (Request $request) {
        // Hanya mengizinkan User (bukan Vendor) untuk mengakses profile ini
        if ($request->user() instanceof \App\Models\User) {
            return response()->json($request->user());
        }
        return response()->json(['message' => 'Unauthorized or invalid scope'], 401);
    });
});

// --- VENDOR API AUTH ---
Route::prefix('vendor')->group(function () {
    
    // Register dan Login Vendor (Untuk Postman)
    Route::post('/register', [VendorAuthController::class, 'register']);
    Route::post('/login', [VendorAuthController::class, 'login']);

    // Route yang dilindungi untuk Vendor
    Route::middleware('auth:sanctum')->group(function () {
        
        Route::get('/profile', function (Request $request) {
            // Memastikan pengguna yang login adalah Vendor
            if ($request->user() instanceof \App\Models\Vendor) {
                 return response()->json($request->user());
            }
            return response()->json(['message' => 'Unauthorized or not a vendor token'], 401);
        });

        // Logout API Vendor
        Route::post('/logout', [VendorAuthController::class, 'logoutApi']); 
        // Anda perlu menambahkan metode 'logoutApi' di VendorAuthController
    });
});


// --- RESOURCE ROUTES (UMUM/ADMIN) ---
// Note: Route di bawah ini perlu middleware 'auth:sanctum' dan pengecekan 'role' admin jika hanya admin yang boleh mengakses.

Route::apiResource('kamars', kamarController::class);
Route::post('/kamars/store', [kamarController::class, 'store']); // Ini tumpang tindih dengan apiResource, sebaiknya dihapus atau diganti

Route::apiResource('category', Categorycontroller::class);
Route::apiResource('/addons', AddonController::class);

Route::apiResource('provinces', ProvinceController::class);
Route::apiResource('cities', CityController::class);
Route::apiResource('products', ProductController::class);

Route::get('/bookings', [BookingsController::class, 'index']); // Sebaiknya di dalam middleware

// Route Booking yang memerlukan otentikasi
Route::prefix('booking')->middleware('auth:sanctum')->group(function () {
    // Route ini sebaiknya menjadi PUT/PATCH jika melakukan perubahan status
    route::post('/checkout/{id}', [BookingsController::class, 'checkout'])->name('booking.checkout'); 
    route::post('/checkin/{id}', [BookingsController::class, 'checkin'])->name('booking.checkin');
    
    route::post('/store', [BookingsController::class, 'store'])->name('booking.store');
    Route::post('/{id}/cancel', [BookingsController::class, 'cancelBooking']);
    Route::post('/fasilitas', [BookingsController::class, 'storefasilitas']); // Nama yang ambigu, lebih baik '/add-fasilitas' atau semacamnya
});
