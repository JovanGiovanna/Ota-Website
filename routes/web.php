<?php

use Illuminate\Support\Facades\Route;
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
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

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
Route::middleware(['super_admin_access'])->group(function () {
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

    Route::get('/super-admin/packages', [PackagesController::class, 'index'])->name('super_admin.packages');
    Route::get('/super-admin/packages/create', [PackagesController::class, 'create'])->name('super_admin.packages.create');
    Route::post('/super-admin/packages', [PackagesController::class, 'store'])->name('super_admin.packages.store');
    Route::get('/super-admin/packages/{package}/edit', [PackagesController::class, 'edit'])->name('super_admin.packages.edit');
    Route::put('/super-admin/packages/{package}', [PackagesController::class, 'update'])->name('super_admin.packages.update');
    Route::delete('/super-admin/packages/{package}', [PackagesController::class, 'destroy'])->name('super_admin.packages.destroy');

    // User Management
    Route::get('/super-admin/vendors', [VendorController::class, 'index'])->name('super_admin.vendors');
    Route::get('/super-admin/vendors/create', [VendorController::class, 'create'])->name('super_admin.vendors.create');
    Route::post('/super-admin/vendors', [VendorController::class, 'store'])->name('super_admin.vendors.store');
    Route::get('/super-admin/vendors/{vendor}/edit', [VendorController::class, 'edit'])->name('super_admin.vendors.edit');
    Route::put('/super-admin/vendors/{vendor}', [VendorController::class, 'update'])->name('super_admin.vendors.update');
    Route::delete('/super-admin/vendors/{vendor}', [VendorController::class, 'destroy'])->name('super_admin.vendors.destroy');

    // Vendor-specific routes
    Route::get('/super-admin/vendors/{vendor}/products', [VendorController::class, 'vendorProducts'])->name('super_admin.vendors.products');
    Route::get('/super-admin/vendors/{vendor}/addons', [VendorController::class, 'vendorAddons'])->name('super_admin.vendors.addons');
    Route::get('/super-admin/vendors/{vendor}/profile', [VendorController::class, 'vendorProfile'])->name('super_admin.vendors.profile');
    Route::get('/super-admin/vendors/{vendor}/transaction-products', [VendorController::class, 'vendorTransactionProducts'])->name('super_admin.vendors.transaction_products');
    Route::get('/super-admin/vendors/{vendor}/transaction-addons', [VendorController::class, 'vendorTransactionAddons'])->name('super_admin.vendors.transaction_addons');

    Route::get('/super-admin/vendor-details', [VendorInfoController::class, 'index'])->name('super_admin.vendor_details');
    Route::get('/super-admin/vendor-details/export', [VendorInfoController::class, 'export'])->name('super_admin.vendor_details.export');
    Route::get('/super-admin/vendor-details/{vendorInfo}', [VendorInfoController::class, 'show'])->name('super_admin.vendor_details.show');
    Route::get('/super-admin/vendor-details/{vendorInfo}/edit', [VendorInfoController::class, 'edit'])->name('super_admin.vendor_details.edit');
    Route::put('/super-admin/vendor-details/{vendorInfo}', [VendorInfoController::class, 'update'])->name('super_admin.vendor_details.update');
    Route::delete('/super-admin/vendor-details/{vendorInfo}', [VendorInfoController::class, 'destroy'])->name('super_admin.vendor_details.destroy');
    Route::get('/super-admin/customers', [SuperAdminController::class, 'customers'])->name('super_admin.customers');
    Route::post('/super-admin/customers/{id}/ban', [SuperAdminController::class, 'banCustomer'])->name('super_admin.customers.ban');
    Route::post('/super-admin/customers/{id}/unban', [SuperAdminController::class, 'unbanCustomer'])->name('super_admin.customers.unban');
    Route::get('/super-admin/customers/{id}/view', [SuperAdminController::class, 'viewCustomer'])->name('super_admin.customers.view');

    // Transaction Management
    Route::get('/super-admin/transaction-packages', [SuperAdminController::class, 'transactionPackages'])->name('super_admin.transaction_packages');
    Route::get('/super-admin/transaction-products', [SuperAdminController::class, 'transactionProducts'])->name('super_admin.transaction_products');
    Route::get('/super-admin/transaction-addons', [SuperAdminController::class, 'transactionAddons'])->name('super_admin.transaction_addons');

    // System Management
    Route::get('/super-admin/rekon', [SuperAdminController::class, 'rekon'])->name('super_admin.rekon');
    Route::get('/super-admin/system-settings', [DashboardController::class, 'systemSettings'])->name('super_admin.system_settings');
});

// Protected routes
Route::middleware(['super_admin_access:admin'])->group(function () {
    // Admin dashboard - accessible by admin or super_admin
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Admin user management routes
    Route::get('/admin/users', [DashboardController::class, 'users'])->name('admin.users');

    // Admin bookings routes
    Route::get('/admin/bookings', [DashboardController::class, 'bookings'])->name('admin.bookings');

    // Admin categories routes
    Route::get('/admin/categories', [DashboardController::class, 'categories'])->name('admin.categories');

    // Admin analytics routes
    Route::get('/admin/analytics', [DashboardController::class, 'analytics'])->name('admin.analytics');

    // Admin settings routes
    Route::get('/admin/settings', [DashboardController::class, 'settings'])->name('admin.settings');
});

Route::middleware(['super_admin_access'])->group(function () {
    // User dashboard - accessible by user or super_admin
    Route::get('/dashboard', function () {
        return view('landing');
    })->name('user.dashboard');
});

Route::middleware(['super_admin_access:vendor'])->group(function () {
    // Vendor dashboard - accessible by vendor or super_admin
    Route::get('/vendor/dashboard', function () {
        return view('vendor.dashboard');
    })->name('vendor.dashboard');

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

    Route::get('/vendor/addons', [VendorController::class, 'vendorAddonsDashboard'])->name('vendor.addons');
    Route::get('/vendor/addons/create', [VendorController::class, 'createAddon'])->name('vendor.addons.create');
    Route::post('/vendor/addons', [VendorController::class, 'storeAddon'])->name('vendor.addons.store');
    Route::get('/vendor/addons/{addon}/edit', [VendorController::class, 'editAddon'])->name('vendor.addons.edit');
    Route::put('/vendor/addons/{addon}', [VendorController::class, 'updateAddon'])->name('vendor.addons.update');
    Route::delete('/vendor/addons/{addon}', [VendorController::class, 'destroyAddon'])->name('vendor.addons.destroy');

    Route::get('/vendor/transaction-products', [VendorController::class, 'vendorTransactionProductsDashboard'])->name('vendor.transaction_products');
    Route::get('/vendor/transaction-addons', [VendorController::class, 'vendorTransactionAddonsDashboard'])->name('vendor.transaction_addons');
});

Route::middleware(['auth:super_admin'])->group(function () {
    // Super Admin dashboard - accessible by super_admin
    Route::get('/super-admin/dashboard', [SuperAdminController::class, 'dashboard'])->name('super_admin.dashboard');
});
