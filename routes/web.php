<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingsController;
use App\Http\Controllers\KamarController;
use App\Http\Controllers\FasilitasController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AddonController;
use App\Http\Controllers\LegalController;




// ================= LANDING =================
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/allrooms', [LandingController::class, 'allrooms'])->name('allrooms');
Route::get('/terms-of-service', [LegalController::class, 'terms'])->name('terms.of.service');
Route::get('/privacy-policy', [LegalController::class, 'policy'])->name('privacy.policy');

// ================= AUTH =================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'loginWeb'])->name('login.web');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Register khusus User via Web
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'registerWeb'])->name('register.web');
Route::get('/kamars', [KamarController::class, 'kamar'])->name('kamars');
Route::get('/fasilitas', [FasilitasController::class, 'fasilitas'])->name('fasilitas');

// ================= USER =================
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/booking/create', [BookingsController::class, 'create'])->name('booking.create');
    Route::get('/booking/create/fasilitas', [BookingsController::class, 'createfasilitas'])->name('bookingfasilitas.create');
    Route::post('/booking/store', [BookingsController::class, 'store'])->name('booking.store');
    Route::post('/booking/storefasilitas', [BookingsController::class, 'storefasilitas'])->name('booking.storefasilitas');
    Route::get('/booking/history', [BookingsController::class, 'history'])->name('booking.history');
    Route::get('/booking/detail/{type}/{id}', [BookingsController::class, 'showDetail'])
        ->name('booking.show.detail') // Nama route yang lebih generik
        ->where('type', 'kamar|fasilitas') // Opsional: Batasi hanya menerima 'kamar' atau 'fasilitas'
        ->where('id', '[0-9]+'); // Batasi id hanya angka

    // Cancel Booking
    Route::post('/booking/cancel/{id}', [BookingsController::class, 'cancelBooking'])->name('booking.cancel');
    Route::post('/booking/cancel/fasilitas/{id}', [BookingsController::class, 'cancelBookingFasilitas'])->name('booking.cancel.fasilitas');
});

// ================= ADMIN =================
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardController::class , 'index'])->name('admin.dashboard');
    // Resource routes
    Route::resource('kamar', KamarController::class);
    Route::resource('fasilitas', FasilitasController::class);
    Route::resource('category', CategoryController::class);



    // Admin booking index
    Route::get('/booking', [BookingsController::class, 'adminIndex'])->name('booking.index');

    // Booking Kamar check-in / check-out
    Route::post('/booking/{id}/checkin', [BookingsController::class, 'checkin'])->name('booking.checkin');
    Route::post('/booking/{id}/checkout', [BookingsController::class, 'checkout'])->name('booking.checkout');
    Route::post('/booking/{id}/selesai', [BookingsController::class, 'setSelesaiKamar'])->name('booking.selesai.kamar');
    Route::post('/booking/{id}/maintenance', [BookingsController::class, 'setMaintenanceKamar'])->name('booking.maintenance.kamar');
    Route::post('/booking/{id}/maintenance/done', [BookingsController::class, 'setMaintenanceDoneKamar'])->name('booking.maintenance.done.kamar');

    Route::resource('addons', AddonController::class)
    // Anda bisa menambahkan middleware di sini untuk membatasi akses hanya untuk Admin/user tertentu
    // ->middleware(['auth', 'can:manage-addons']); 
    ->names([
        'index'   => 'addons.index',
        'create'  => 'addons.create',
        'store'   => 'addons.store',
        'show'    => 'addons.show',
        'edit'    => 'addons.edit',
        'update'  => 'addons.update',
        'destroy' => 'addons.destroy',
    ]);

    // Booking Fasilitas check-in / check-out
    Route::post('/booking/fasilitas/{id}/checkin', [BookingsController::class, 'checkinFasilitas'])->name('booking.checkin.fasilitas');
    Route::post('/booking/fasilitas/{id}/checkout', [BookingsController::class, 'checkoutFasilitas'])->name('booking.checkout.fasilitas');
    Route::post('/booking-fasilitas/{id}/selesai', [BookingsController::class, 'setSelesaiFasilitas'])->name('booking.selesai.fasilitas');
    Route::post('/booking-fasilitas/{id}/maintenance', [BookingsController::class, 'setMaintenanceFasilitas'])->name('booking.maintenance.fasilitas');
    Route::post('/booking-fasilitas/{id}/maintenance/done', [BookingsController::class, 'setMaintenanceDoneFasilitas'])->name('booking.maintenance.done.fasilitas');

    // **Approve / Reject Cancel Kamar**
    Route::post('/booking/{id}/cancel/approve', [BookingsController::class, 'approveCancel'])->name('booking.cancel.approve');
    Route::post('/booking/{id}/cancel/reject', [BookingsController::class, 'rejectCancel'])->name('booking.cancel.reject');

    // **Approve / Reject Cancel Fasilitas**
    Route::post('/booking/fasilitas/{id}/cancel/approve', [BookingsController::class, 'approveCancelFasilitas'])->name('booking.cancel.fasilitas.approve');
    Route::post('/booking/fasilitas/{id}/cancel/reject', [BookingsController::class, 'rejectCancelFasilitas'])->name('booking.cancel.fasilitas.reject');

    // Laporan stok kamar
    Route::get('/laporan/stok', [KamarController::class, 'laporanStok'])->name('laporan.stok');
});


