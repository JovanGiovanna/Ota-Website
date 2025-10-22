<?php

use App\Http\Controllers\Categorycontroller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VendorAuthController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\BookingsController;
use App\Http\Controllers\AddonController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PackagesController;
use App\Http\Controllers\TypesController;
use App\Http\Controllers\PackageProductController;
use App\Http\Controllers\PackageAddonController;
use App\Http\Controllers\DetailBookingController;
use App\Http\Controllers\BookingAddonController;
use App\Http\Controllers\BookPackageAddonController;
use App\Http\Controllers\VendorInfoController;



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
    Route::post('/logout', [AuthController::class, 'logoutApi']);
    Route::get('/profile', function (Request $request) {
        // Hanya mengizinkan User (bukan Vendor) untuk mengakses profile ini
        if ($request->user() instanceof \App\Models\User) {
            return response()->json($request->user());
        }
        return response()->json(['message' => 'Unauthorized or invalid scope'], 401);
    });
});

// =======================================================
// A. ROUTE AUTHENTIKASI (LOGIN & LOGOUT) - Tanpa Middleware
// =======================================================

Route::group(['prefix' => 'super-admin', 'as' => 'api.super_admin.'], function () {
    // POST /api/super-admin/register (maps to SuperAdminController@registerApi)
    // Used to create a new Super Admin account and generate an access token
    Route::post('register', [SuperAdminController::class, 'registerApi'])->name('register');
    
    // POST /api/super-admin/login (maps to SuperAdminController@loginApi)
    // Used to authenticate an existing Super Admin and generate an access token
    Route::post('login', [SuperAdminController::class, 'loginApi'])->name('login');
});



// --- VENDOR API AUTH ---
Route::prefix('/vendor')->group(function () {
    
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
    });
});


// --- RESOURCE ROUTES (UMUM/ADMIN) ---
// Note: Route di bawah ini perlu middleware 'auth:sanctum' dan pengecekan 'role' admin jika hanya admin yang boleh mengakses.
Route::resource('packages', PackagesController::class);
Route::resource('types', TypesController::class);
Route::apiResource('category', Categorycontroller::class);
Route::apiResource('/addons', AddonController::class);
Route::resource('package-products', PackageProductController::class)->except(['create', 'edit', 'update']);
Route::resource('package-addon', PackageAddonController::class)->except(['create', 'edit', 'update']);
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
});

// Routes untuk Detail Booking
Route::apiResource('detail-bookings', DetailBookingController::class);

// Routes untuk Booking Addons
Route::prefix('booking-addons')->group(function () {
    Route::get('/', [BookingAddonController::class, 'index']);
    Route::post('/', [BookingAddonController::class, 'store']);
    Route::get('/{bookingId}', [BookingAddonController::class, 'show']);
    Route::put('/{bookingId}/{addonId}', [BookingAddonController::class, 'update']);
    Route::delete('/{bookingId}/{addonId}', [BookingAddonController::class, 'destroy']);
});

// Routes untuk Book Package Addons
Route::prefix('book-package-addons')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [BookPackageAddonController::class, 'index']);
    Route::post('/', [BookPackageAddonController::class, 'store']);
    Route::get('/{id}', [BookPackageAddonController::class, 'show']);
    Route::put('/{id}', [BookPackageAddonController::class, 'update']);
    Route::delete('/{id}', [BookPackageAddonController::class, 'destroy']);
    Route::get('/booking/{bookingId}', [BookPackageAddonController::class, 'getByBooking']);
    Route::get('/addon/{addonId}', [BookPackageAddonController::class, 'getByAddon']);
});

// Routes untuk Vendor Info
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/vendor-infos', [VendorInfoController::class, 'index']);
    Route::post('/vendor-infos', [VendorInfoController::class, 'store']);
    Route::get('/vendor-infos/{id}', [VendorInfoController::class, 'show']);
    Route::put('/vendor-infos/{id}', [VendorInfoController::class, 'update']);
    Route::delete('/vendor-infos/{id}', [VendorInfoController::class, 'destroy']);
    Route::get('/vendor-infos/vendor/{vendorId}', [VendorInfoController::class, 'getByVendor']);
    Route::get('/vendor-infos/city/{cityId}', [VendorInfoController::class, 'getByCity']);
});
