<?php

use Illuminate\Support\Facades\Route;
use App\Notifications\EmailNotification;
use App\Models\User;
use App\Models\Booking;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\AddonController;
use App\Http\Controllers\TypesController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BookingsController;
use App\Http\Controllers\PackagesController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\VendorAuthController;
use App\Http\Controllers\VendorInfoController;
use App\Http\Controllers\DetailBookingController;
use App\Http\Controllers\BookPackageAddonController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\WishlistController;

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

// Landing page
Route::get('/', [LandingController::class, 'index'])->name('landing');

// User authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'loginWeb'])->name('login.web');
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'registerWeb'])->name('register.web');
Route::post('/check-email', [AuthController::class, 'checkEmail'])->name('check.email');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

//notification
Route::get('/test-user-email', function () {
    $user = User::first(); 
    $booking = Booking::first(); 

    if (!$user || !$booking) {
        return "ERROR: Pastikan ada data User dan Booking di database.";
    }

    $user->notify(new EmailNotification($booking));

    return "Email konfirmasi booking #{$booking->id} dikirim ke Mailtrap (penerima: {$user->email})!";
});

// Admin authentication routes
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'loginWeb'])->name('admin.login.web');
Route::get('/admin/register', [AdminAuthController::class, 'showRegistrationForm'])->name('admin.register');
Route::post('/admin/register', [AdminAuthController::class, 'registerWeb'])->name('admin.register.web');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Vendor authentication routes
Route::get('/vendor/login', [VendorAuthController::class, 'showLoginForm'])->name('vendor.login');
Route::post('/vendor/login', [VendorAuthController::class, 'loginWeb'])->name('vendor.login.web');
Route::get('/vendor/register', [VendorAuthController::class, 'showRegistrationForm'])->name('vendor.register');
Route::post('/vendor/register', [VendorAuthController::class, 'registerWeb'])->name('vendor.register.web');
Route::post('/vendor/logout', [VendorAuthController::class, 'logout'])->name('vendor.logout');

// Super Admin authentication routes
Route::get('/super-admin/login', [SuperAdminController::class, 'showLoginForm'])->name('super_admin.login');
Route::post('/super-admin/login', [SuperAdminController::class, 'loginWeb'])->name('super_admin.login.web');
Route::get('/super-admin/register', [SuperAdminController::class, 'showRegistrationForm'])->name('super_admin.register');
Route::post('/super-admin/register', [SuperAdminController::class, 'registerWeb'])->name('super_admin.register.web');
Route::post('/super-admin/logout', [SuperAdminController::class, 'logoutWeb'])->name('super_admin.logout');

// Super Admin management routes
Route::middleware(['super_admin_access:admin'])->group(function () {
    // Location Management
    Route::get('/super-admin/provinces', [ProvinceController::class, 'index'])->name('super_admin.provinces');
    Route::get('/super-admin/provinces/create', [ProvinceController::class, 'create'])->name('super_admin.provinces.create');
    Route::post('/super-admin/provinces', [ProvinceController::class, 'store'])->name('super_admin.provinces.store');
    Route::get('/super-admin/provinces/{province}/edit', [ProvinceController::class, 'edit'])->name('super_admin.provinces.edit');
    Route::put('/super-admin/provinces/{province}', [ProvinceController::class, 'update'])->name('super_admin.provinces.update');
    Route::delete('/super-admin/provinces/{province}', [ProvinceController::class, 'destroy'])->name('super_admin.provinces.destroy');

    Route::get('/super-admin/cities', [CityController::class, 'index'])->name('super_admin.cities');
    Route::get('/super-admin/cities/create', [CityController::class, 'create'])->name('super_admin.cities.create');
    Route::post('/super-admin/cities', [CityController::class, 'store'])->name('super_admin.cities.store');
    Route::get('/super-admin/cities/{city}/edit', [CityController::class, 'edit'])->name('super_admin.cities.edit');
    Route::put('/super-admin/cities/{city}', [CityController::class, 'update'])->name('super_admin.cities.update');
    Route::delete('/super-admin/cities/{city}', [CityController::class, 'destroy'])->name('super_admin.cities.destroy');

    // Category & Type Management   
    Route::get('/super-admin/types-categories', [TypesController::class, 'index'])->name('super_admin.types_categories');
    Route::get('/super-admin/types/create', [TypesController::class, 'create'])->name('super_admin.types.create');
    Route::post('/super-admin/types', [TypesController::class, 'store'])->name('super_admin.types.store');
    Route::get('/super-admin/types/{type}/edit', [TypesController::class, 'edit'])->name('super_admin.types.edit');
    Route::put('/super-admin/types/{type}', [TypesController::class, 'update'])->name('super_admin.types.update');
    Route::delete('/super-admin/types/{type}', [TypesController::class, 'destroy'])->name('super_admin.types.destroy');

    Route::get('/super-admin/categories/create', [CategoryController::class, 'create'])->name('super_admin.categories.create');
    Route::post('/super-admin/categories', [CategoryController::class, 'store'])->name('super_admin.categories.store');
    Route::get('/super-admin/categories/{category}/edit', [CategoryController::class, 'edit'])->name('super_admin.categories.edit');
    Route::put('/super-admin/categories/{category}', [CategoryController::class, 'update'])->name('super_admin.categories.update');
    Route::delete('/super-admin/categories/{category}', [CategoryController::class, 'destroy'])->name('super_admin.categories.destroy');

    Route::get('/super-admin/packages', [PackagesController::class, 'index'])->name('super_admin.packages')->middleware('admin.permission:packages.manage');
    Route::get('/super-admin/packages/create', [PackagesController::class, 'create'])->name('super_admin.packages.create')->middleware('admin.permission:packages.manage');
    Route::post('/super-admin/packages', [PackagesController::class, 'store'])->name('super_admin.packages.store')->middleware('admin.permission:packages.manage');
    Route::get('/super-admin/packages/{package}/edit', [PackagesController::class, 'edit'])->name('super_admin.packages.edit')->middleware('admin.permission:packages.manage');
    Route::put('/super-admin/packages/{package}', [PackagesController::class, 'update'])->name('super_admin.packages.update')->middleware('admin.permission:packages.manage');
    Route::delete('/super-admin/packages/{package}', [PackagesController::class, 'destroy'])->name('super_admin.packages.destroy')->middleware('admin.permission:packages.manage');

    // Product Management (Top-Level)
    Route::get('/super-admin/products', [VendorController::class, 'allProducts'])->name('super_admin.products')->middleware('admin.permission:products.manage');
    Route::post('/super-admin/products/{product}/stock', [VendorController::class, 'addProductStockTop'])->name('super_admin.products.stock')->middleware('admin.permission:products.manage');
    Route::get('/super-admin/products/{product}/edit', [VendorController::class, 'editProductTop'])->name('super_admin.products.edit')->middleware('admin.permission:products.manage');
    Route::put('/super-admin/products/{product}', [VendorController::class, 'updateProductTop'])->name('super_admin.products.update')->middleware('admin.permission:products.manage');
    Route::delete('/super-admin/products/{product}', [VendorController::class, 'deleteProductTop'])->name('super_admin.products.destroy')->middleware('admin.permission:products.manage');

    // Addon Management (Top-Level)
    Route::get('/super-admin/addons', [VendorController::class, 'allAddons'])->name('super_admin.addons')->middleware('admin.permission:addons.manage');
    Route::get('/super-admin/addons/{addon}/edit', [VendorController::class, 'editAddonTop'])->name('super_admin.addons.edit')->middleware('admin.permission:addons.manage');
    Route::put('/super-admin/addons/{addon}', [VendorController::class, 'updateAddonTop'])->name('super_admin.addons.update')->middleware('admin.permission:addons.manage');
    Route::delete('/super-admin/addons/{addon}', [VendorController::class, 'deleteAddonTop'])->name('super_admin.addons.destroy')->middleware('admin.permission:addons.manage');

    // User Management
    Route::get('/super-admin/vendors', [VendorController::class, 'index'])->name('super_admin.vendors')->middleware('admin.permission:vendors.manage');
    Route::get('/super-admin/vendors/create', [VendorController::class, 'create'])->name('super_admin.vendors.create')->middleware('admin.permission:vendors.manage');
    Route::post('/super-admin/vendors', [VendorController::class, 'store'])->name('super_admin.vendors.store')->middleware('admin.permission:vendors.manage');
    Route::get('/super-admin/vendors/{vendor}/edit', [VendorController::class, 'edit'])->name('super_admin.vendors.edit')->middleware('admin.permission:vendors.manage');
    Route::put('/super-admin/vendors/{vendor}', [VendorController::class, 'update'])->name('super_admin.vendors.update')->middleware('admin.permission:vendors.manage');
    Route::delete('/super-admin/vendors/{vendor}', [VendorController::class, 'destroy'])->name('super_admin.vendors.destroy')->middleware('admin.permission:vendors.manage');

    // Vendor-specific routes
    Route::get('/super-admin/vendors/{vendor}/products', [VendorController::class, 'vendorProducts'])->name('super_admin.vendors.products');
    Route::get('/super-admin/vendors/{vendor}/products/{product}/detail', [VendorController::class, 'vendorProductDetail'])->name('super_admin.vendors.products.detail');
    Route::get('/super-admin/vendors/{vendor}/products/{product}/edit', [VendorController::class, 'editProductAdmin'])->name('super_admin.vendors.products.edit');
    Route::put('/super-admin/vendors/{vendor}/products/{product}', [VendorController::class, 'updateProductAdmin'])->name('super_admin.vendors.products.update');
    Route::delete('/super-admin/vendors/{vendor}/products/{product}', [VendorController::class, 'deleteProductAdmin'])->name('super_admin.vendors.products.destroy');
    
    Route::get('/super-admin/vendors/{vendor}/addons', [VendorController::class, 'vendorAddons'])->name('super_admin.vendors.addons');
    Route::get('/super-admin/vendors/{vendor}/addons/{addon}/details', [VendorController::class, 'vendorAddonDetails'])->name('super_admin.vendors.addons.details');
    Route::get('/super-admin/vendors/{vendor}/addons/{addon}/detail', [VendorController::class, 'vendorAddonDetail'])->name('super_admin.vendors.addons.detail');
    Route::get('/super-admin/vendors/{vendor}/addons/{addon}/edit', [VendorController::class, 'editAddonAdmin'])->name('super_admin.vendors.addons.edit');
    Route::put('/super-admin/vendors/{vendor}/addons/{addon}', [VendorController::class, 'updateAddonAdmin'])->name('super_admin.vendors.addons.update');
    Route::delete('/super-admin/vendors/{vendor}/addons/{addon}', [VendorController::class, 'deleteAddonAdmin'])->name('super_admin.vendors.addons.destroy');
    
    Route::get('/super-admin/vendors/{vendor}/profile', [VendorController::class, 'vendorProfile'])->name('super_admin.vendors.profile');
    Route::get('/super-admin/vendors/{vendor}/transaction-products', [VendorController::class, 'vendorTransactionProducts'])->name('super_admin.vendors.transaction_products');
    Route::get('/super-admin/vendors/{vendor}/transaction-addons', [VendorController::class, 'vendorTransactionAddons'])->name('super_admin.vendors.transaction_addons');

    Route::get('/super-admin/vendor-details', [VendorInfoController::class, 'index'])->name('super_admin.vendor_details')->middleware('admin.permission:vendors.manage');
    Route::get('/super-admin/vendor-details/export', [VendorInfoController::class, 'export'])->name('super_admin.vendor_details.export')->middleware('admin.permission:vendors.manage');
    Route::get('/super-admin/vendor-details/{vendorInfo}', [VendorInfoController::class, 'show'])->name('super_admin.vendor_details.show')->middleware('admin.permission:vendors.manage');
    Route::get('/super-admin/vendor-details/{vendorInfo}/edit', [VendorInfoController::class, 'edit'])->name('super_admin.vendor_details.edit')->middleware('admin.permission:vendors.manage');
    Route::put('/super-admin/vendor-details/{vendorInfo}', [VendorInfoController::class, 'update'])->name('super_admin.vendor_details.update')->middleware('admin.permission:vendors.manage');
    Route::delete('/super-admin/vendor-details/{vendorInfo}', [VendorInfoController::class, 'destroy'])->name('super_admin.vendor_details.destroy')->middleware('admin.permission:vendors.manage');
    Route::get('/super-admin/customers', [SuperAdminController::class, 'customers'])->name('super_admin.customers')->middleware('admin.permission:customers.manage');
    Route::post('/super-admin/customers/{id}/ban', [SuperAdminController::class, 'banCustomer'])->name('super_admin.customers.ban')->middleware('admin.permission:customers.manage');
    Route::post('/super-admin/customers/{id}/unban', [SuperAdminController::class, 'unbanCustomer'])->name('super_admin.customers.unban')->middleware('admin.permission:customers.manage');
    Route::get('/super-admin/customers/{id}/view', [SuperAdminController::class, 'viewCustomer'])->name('super_admin.customers.view')->middleware('admin.permission:customers.manage');

    // Transaction Management
    Route::get('/super-admin/transaction-packages', [SuperAdminController::class, 'transactionPackages'])->name('super_admin.transaction_packages')->middleware('admin.permission:transactions.view');
    Route::get('/super-admin/transaction-products', [SuperAdminController::class, 'transactionProducts'])->name('super_admin.transaction_products')->middleware('admin.permission:transactions.view');
    Route::get('/super-admin/transaction-addons', [SuperAdminController::class, 'transactionAddons'])->name('super_admin.transaction_addons')->middleware('admin.permission:transactions.view');

    // Unified Bookings Management (Super Admin) - dedicated routes
    Route::get('/super-admin/bookings', [BookingsController::class, 'index'])->name('super_admin.bookings')->middleware('admin.permission:transactions.view');
    Route::get('/super-admin/bookings/{booking}/detail', [BookingsController::class, 'showDetailAdmin'])->name('super_admin.bookings.detail')->middleware('admin.permission:transactions.view');
    Route::post('/super-admin/bookings/{booking}/approve', [BookingsController::class, 'approve'])->name('super_admin.bookings.approve')->middleware('admin.permission:transactions.manage');
    Route::post('/super-admin/bookings/{booking}/reject', [BookingsController::class, 'reject'])->name('super_admin.bookings.reject')->middleware('admin.permission:transactions.manage');
    Route::post('/super-admin/bookings/{booking}/verify-payment', [BookingsController::class, 'verifyPayment'])->name('super_admin.bookings.verify_payment')->middleware('admin.permission:transactions.manage');
    Route::post('/super-admin/bookings/{booking}/process-refund', [BookingsController::class, 'processRefund'])->name('super_admin.bookings.process_refund')->middleware('admin.permission:transactions.manage');

    // System Management
    Route::get('/super-admin/rekon', [SuperAdminController::class, 'rekon'])->name('super_admin.rekon')->middleware('admin.permission:system.manage');
    Route::get('/super-admin/system-settings', [DashboardController::class, 'systemSettings'])->name('super_admin.system_settings')->middleware('admin.permission:system.manage');

    // Admin management (Super Admin)
    Route::get('/super-admin/admins', [SuperAdminController::class, 'adminsIndex'])->name('super_admin.admins')->middleware('admin.permission:admins.manage');
    Route::get('/super-admin/admins/create', [SuperAdminController::class, 'createAdminForm'])->name('super_admin.admins.create')->middleware('admin.permission:admins.manage');
    Route::post('/super-admin/admins', [SuperAdminController::class, 'storeAdmin'])->name('super_admin.admins.store')->middleware('admin.permission:admins.manage');
    Route::get('/super-admin/admins/{admin}/edit', [SuperAdminController::class, 'editAdminForm'])->name('super_admin.admins.edit')->middleware('admin.permission:admins.manage');
    Route::put('/super-admin/admins/{admin}', [SuperAdminController::class, 'updateAdmin'])->name('super_admin.admins.update')->middleware('admin.permission:admins.manage');
    Route::delete('/super-admin/admins/{admin}', [SuperAdminController::class, 'destroyAdmin'])->name('super_admin.admins.destroy')->middleware('admin.permission:admins.manage');
});

// Protected routes
Route::middleware(['super_admin_access:admin']) ->prefix('admin')
    ->name('admin.')->group(function () {
    // Admin dashboard - accessible by admin or super_admin
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin user management routes
    Route::get('/users', [DashboardController::class, 'users'])->name('users');

    // Admin bookings routes
    Route::get('/bookings', [DashboardController::class, 'bookings'])->name('bookings');

    // Admin categories routes
    Route::get('/packages', [DashboardController::class, 'packages'])->name('packages');
    Route::post('/packages/store', [DashboardController::class, 'store'])->name('packages.store');
    Route::get('/packages/create', [DashboardController::class, 'packagesCreate'])->name('packages.create');
    Route::get('/packages/{package}/edit', [DashboardController::class, 'packagesUpdate'])->name('packages.edit'); // Edit Form
    Route::put('/packages/{package}', [DashboardController::class, 'update'])->name('packages.update'); // Update Data
    Route::delete('/packages/{package}', [DashboardController::class, 'destroy'])->name('packages.destroy'); // Delete Data    // Admin analytics routes
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');

    // Admin settings routes
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');

    Route::get('/profile', [AdminAuthController::class, 'showProfilePage'])->name('profile');
    Route::get('/profile/edit', [AdminAuthController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile/update', [AdminAuthController::class, 'updateProfile'])->name('profile.update');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});
Route::prefix('super-admin/transactions')->middleware(['super_admin_access:admin'])->name('super_admin.transaction.')->group(function () {
    // Unified transactions index for Super Admin
    Route::get('/', [BookingsController::class, 'index'])->name('index')->middleware('admin.permission:transactions.view');
    // Index sudah ada di Controller Anda: $bookings = Booking::with...->paginate(10);
    Route::get('/packages', [BookingsController::class, 'index'])->name('packages'); // Mengarah ke view 'super_admin.transaction_packages'

    // Route Approval BARU
    Route::post('/{booking}/approve', [BookingsController::class, 'approve'])->name('approve');

    // Route Rejection BARU
    Route::post('/{booking}/reject', [BookingsController::class, 'reject'])->name('reject');

    Route::get('/{booking}/detail', [BookingsController::class, 'showDetailAdmin'])->name('detail');

    // Route Detail (Anda mungkin ingin membuat fungsi detail khusus Admin)
    // Route::get('/{booking}/detail', [BookingsController::class, 'showDetailAdmin'])->name('detail');
    // Verify payment (admin) -> move from 'paid' to 'completed'
    Route::post('/{booking}/verify-payment', [BookingsController::class, 'verifyPayment'])->name('verify_payment')->middleware('admin.permission:transactions.manage');
    // Process refund (admin) -> process return and mark complete
    Route::post('/{booking}/process-refund', [BookingsController::class, 'processRefund'])->name('process_refund')->middleware('admin.permission:transactions.manage');
});

// Admin transaction routes
Route::middleware(['super_admin_access:admin'])->prefix('admin/transactions')->name('admin.transaction.')->group(function () {
    Route::put('/{booking}/updateStatus', [BookingsController::class, 'updateStatus'])->name('updateStatus');
});
Route::prefix('addons')->name('super_admin.addon.')->group(function () {
        // Index Semua Booking Addon
        // URL: /super-admin/addons/IndexAddons
        Route::get('/IndexAddons', [BookingsController::class, 'indexAddonsOnly'])->name('index'); 

        // Route Detail (Re-use fungsi showDetailAdmin)
        // URL: /super-admin/addons/{booking}/detail
        Route::get('/{booking}/detail', [BookingsController::class, 'showDetailAdmin'])->name('detail');

        // ROUTE APPROVE DAN REJECT KHUSUS ADDON
        Route::post('/{booking}/approve', [BookingsController::class, 'approve'])->name('approve');
        Route::post('/{booking}/reject', [BookingsController::class, 'reject'])->name('reject');
    });
    
Route::prefix('packages')->name('super_admin.package.')->group(function () {
        // Index Semua Booking Package
        Route::get('/IndexPackages', [BookingsController::class, 'indexPackagesOnly'])->name('index'); 

        // Index Khusus Approval Booking Package (Status PENDING)
        Route::get('/approval', [BookingsController::class, 'indexPackageApproval'])->name('approval'); 

        // Re-use fungsi approve/reject dari Controller (menggunakan nama route packages.)
        Route::post('/{booking}/approve', [BookingsController::class, 'approve'])->name('approve');
        Route::post('/{booking}/reject', [BookingsController::class, 'reject'])->name('reject');
    });

Route::prefix('products')->name('super_admin.product.')->group(function () {
        // Index Semua Booking Product
        // URL: /super-admin/products/IndexProducts
        Route::get('/IndexProducts', [BookingsController::class, 'indexProductsOnly'])->name('index'); 

        // Route Detail (Re-use fungsi yang sama)
        // URL: /super-admin/products/{booking}/detail
        Route::get('/{booking}/detail', [BookingsController::class, 'showDetailAdmin'])->name('detail');
        Route::post('/{booking}/approve', [BookingsController::class, 'approve'])->name('approve');
        Route::post('/{booking}/reject', [BookingsController::class, 'reject'])->name('reject');
    });


// Search route
Route::get('/search', [SearchController::class, 'index'])->name('user.search');

// Product and Addon detail routes - accessible without authentication
Route::get('/product/{product}', [ProductController::class, 'showDetail'])->name('user.product_detail');
Route::get('/addon/{addon}', [AddonController::class, 'showDetail'])->name('user.addon_detail');
Route::get('/package/{package}', [PackagesController::class, 'showDetail'])->name('user.package_detail');

// Booking routes - accessible without authentication for guest booking
Route::get('/book', function () {
    $packages = \App\Models\Package::where('is_active', true)->get();
    $products = \App\Models\Product::where('status', 'available')->get();
    $addons = \App\Models\Addon::where('status', 'publish')->get();

    return view('user.form_booker', compact('packages', 'products', 'addons'));
})->name('user.form_booker');

Route::post('/book', [BookingsController::class, 'store'])->name('user.book');

// Payment and booking detail routes - accessible without authentication for guest bookings
Route::get('/payment/{booking}', [BookingsController::class, 'payment'])->name('user.payment');
Route::post('/payment/{booking}/confirm', [BookingsController::class, 'confirmPayment'])->name('user.payment.confirm');
Route::post('/payment/{booking}/request-refund', [BookingsController::class, 'requestRefund'])->name('user.payment.request_refund');
Route::get('/history/{booking}', [BookingsController::class, 'showDetail'])->name('user.detail_history');

Route::middleware(['auth'])->group(function () {
    // User pages
    Route::get('/home', [App\Http\Controllers\SearchController::class, 'index'])->name('user.home');

    Route::get('/profile', function () {
        return view('user.profil');
    })->name('user.profil');

    Route::put('/profile/update', [AuthController::class, 'updateProfile'])->name('user.profile.update');
    Route::put('/profile/change-password', [AuthController::class, 'changePassword'])->name('user.profile.change_password');

    Route::get('/history', [BookingsController::class, 'history'])->name('user.history');
    Route::get('/account/activate/{token}', [BookingsController::class, 'activateAccount'])->name('user.account.activate');
    Route::post('/account/activate/{token}', [BookingsController::class, 'setPassword'])->name('user.account.set_password');
    Route::get('/support/{booking}', [BookingsController::class, 'support'])->name('user.support');
    Route::post('/support/{booking}', [BookingsController::class, 'submitSupport'])->name('user.support.submit');
    Route::delete('/booking/cancel/{booking}', [BookingsController::class, 'cancel'])->name('booking.cancel');

    // User dashboard - accessible by user
    Route::get('/dashboard', function () {
        return redirect()->route('user.search');
    })->name('user.dashboard');

    // Review routes
    Route::post('/reviews/{bookingId}', [ReviewController::class, 'store'])->name('reviews.store');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Wishlist routes
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('user.wishlist');
    Route::post('/wishlist/add', [WishlistController::class, 'add'])->name('wishlist.add');
    Route::post('/wishlist/remove', [WishlistController::class, 'remove'])->name('wishlist.remove');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::post('/wishlist/clear-all', [WishlistController::class, 'clearAll'])->name('wishlist.clear_all');
});

Route::middleware(['super_admin_access:vendor'])->group(function () {
    // Vendor dashboard - accessible by vendor or super_admin
    Route::get('/vendor/dashboard', [VendorController::class, 'dashboard'])->name('vendor.dashboard');

    // Vendor info routes
    Route::get('/vendor/info', [VendorInfoController::class, 'showInfoForm'])->name('vendor.info');
    Route::post('/vendor/info', [VendorInfoController::class, 'storeInfo'])->name('vendor.info.store');
    Route::get('/vendor/info/edit', [VendorInfoController::class, 'editInfoForm'])->name('vendor.info.edit');
    Route::put('/vendor/info', [VendorInfoController::class, 'updateInfo'])->name('vendor.info.update');

    // Vendor profile routes
    Route::get('/vendor/profile', [VendorController::class, 'profile'])->name('vendor.profile');
    Route::post('/vendor/profile', [VendorController::class, 'updateProfile'])->name('vendor.profile.update');

    // Vendor bookings routes
    Route::get('/vendor/bookings', [VendorController::class, 'bookings'])->name('vendor.bookings');

    // Vendor services routes
    Route::get('/vendor/services', [VendorController::class, 'services'])->name('vendor.services');
    Route::post('/vendor/services', [VendorController::class, 'storeService'])->name('vendor.services.store');
    Route::put('/vendor/services/{service}', [VendorController::class, 'updateService'])->name('vendor.services.update');
    Route::delete('/vendor/services/{service}', [VendorController::class, 'deleteService'])->name('vendor.services.delete');

    // Vendor pricing routes
    Route::get('/vendor/pricing', [VendorController::class, 'pricing'])->name('vendor.pricing');
    Route::post('/vendor/pricing', [VendorController::class, 'updatePricing'])->name('vendor.pricing.update');

    // Vendor availability routes
    Route::get('/vendor/availability', [VendorController::class, 'availability'])->name('vendor.availability');
    Route::post('/vendor/availability', [VendorController::class, 'updateAvailability'])->name('vendor.availability.update');

    // Vendor analytics routes
    Route::get('/vendor/analytics', [VendorController::class, 'analytics'])->name('vendor.analytics');

    // Vendor products and addons routes
    Route::get('/vendor/products', [VendorController::class, 'vendorProductsDashboard'])->name('vendor.products');
    Route::get('/vendor/products/create', [VendorController::class, 'createProduct'])->name('vendor.products.create');
    Route::post('/vendor/products', [VendorController::class, 'storeProduct'])->name('vendor.products.store');
    Route::get('/vendor/products/{product}/edit', [VendorController::class, 'editProduct'])->name('vendor.products.edit');
    Route::put('/vendor/products/{product}', [VendorController::class, 'updateProduct'])->name('vendor.products.update');
    Route::delete('/vendor/products/{product}', [VendorController::class, 'destroyProduct'])->name('vendor.products.destroy');
    // Vendor stock management
    Route::get('/vendor/stock', [VendorController::class, 'vendorStock'])->name('vendor.stock');
    Route::post('/vendor/products/{product}/stock', [VendorController::class, 'addProductStockVendor'])->name('vendor.products.stock');

    Route::get('/vendor/addons', [VendorController::class, 'vendorAddonsDashboard'])->name('vendor.addons');
    // Vendor transactions report
    Route::get('/vendor/transactions/report', [VendorController::class, 'transactionReport'])->name('vendor.transactions.report');
    Route::get('/vendor/transactions/report/export', [VendorController::class, 'transactionReportExport'])->name('vendor.transactions.report.export');
    Route::get('/vendor/addons/create', [VendorController::class, 'createAddon'])->name('vendor.addons.create');
    Route::post('/vendor/addons', [VendorController::class, 'storeAddon'])->name('vendor.addons.store');
    Route::get('/vendor/addons/{addon}/edit', [VendorController::class, 'editAddon'])->name('vendor.addons.edit');
    Route::put('/vendor/addons/{addon}', [VendorController::class, 'updateAddon'])->name('vendor.addons.update');
    Route::delete('/vendor/addons/{addon}', [VendorController::class, 'destroyAddon'])->name('vendor.addons.destroy');

    Route::get('/vendor/transaction-products', [VendorController::class, 'vendorTransactionProductsDashboard'])->name('vendor.transaction_products');
    Route::get('/vendor/transaction-addons', [VendorController::class, 'vendorTransactionAddonsDashboard'])->name('vendor.transaction_addons');
});

// Allow super_admin and admin (admin guard) to access the super-admin dashboard
Route::middleware(['super_admin_access:admin'])->group(function () {
    Route::get('/super-admin/dashboard', [SuperAdminController::class, 'dashboard'])->name('super_admin.dashboard');
    
    // Email Notification Settings
    Route::get('/super-admin/email-settings', [App\Http\Controllers\EmailSettingController::class, 'index'])->name('super_admin.email_settings');
    Route::post('/super-admin/email-settings', [App\Http\Controllers\EmailSettingController::class, 'store'])->name('super_admin.email_settings.store');
    Route::put('/super-admin/email-settings/{id}', [App\Http\Controllers\EmailSettingController::class, 'update'])->name('super_admin.email_settings.update');
    Route::patch('/super-admin/email-settings/{id}/toggle', [App\Http\Controllers\EmailSettingController::class, 'toggleStatus'])->name('super_admin.email_settings.toggle');
    Route::delete('/super-admin/email-settings/{id}', [App\Http\Controllers\EmailSettingController::class, 'destroy'])->name('super_admin.email_settings.destroy');
});

Route::get('/invoice/download/{bookingId}', [InvoiceController::class, 'download'])->name('invoice.download')->middleware('auth');
