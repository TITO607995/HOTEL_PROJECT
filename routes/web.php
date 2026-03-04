<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ProfileController,
    ReservationController,
    RoomController,
    EmployeeController,
    RoleController,
    ReportController,
    TransactionController,
    PaymentController,
    DashboardController,
    DeviceController
};
use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES & AUTH
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (MUST LOGIN)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // --- CORE SYSTEM ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/about', function () {
        return view('about.about');
    })->name('about.about');

    // --- ROOM MANAGEMENT ---
    Route::prefix('rooms')->group(function () {
        Route::get('/', [RoomController::class, 'index'])->name('rooms.index');
        Route::post('/store', [RoomController::class, 'store'])->name('rooms.store');
        Route::get('/maintenance', [RoomController::class, 'maintenancePage'])->name('rooms.maintenance');
        Route::post('/maintenance/{id}/update', [RoomController::class, 'updateMaintenance'])->name('rooms.maintenance.update');
    });

    // --- GUEST MANAGEMENT ---
    Route::prefix('guests')->group(function () {
        Route::get('/', [ReservationController::class, 'guestIndex'])->name('guests.index');
        Route::put('/{id}', [ReservationController::class, 'updateGuest'])->name('guests.update');
        Route::delete('/bulk-delete', [ReservationController::class, 'bulkDelete'])->name('guests.bulk-delete');
        Route::delete('/{id}', [ReservationController::class, 'destroyGuest'])->name('guests.destroy');
        Route::post('/incognito/{id}', [ReservationController::class, 'toggleIncognito'])->name('guests.toggle-incognito');
    });

    // --- RESERVATION SYSTEM ---
    Route::prefix('reservasi')->group(function () {
        Route::get('/', [ReservationController::class, 'index'])->name('reservations.index');
        Route::get('/tambah', [ReservationController::class, 'create'])->name('reservations.create');
        Route::post('/simpan', [ReservationController::class, 'store'])->name('reservations.store');
        Route::post('/perpanjang/{id}', [ReservationController::class, 'extend'])->name('reservations.extend');
        Route::get('/registration', [ReservationController::class, 'registration'])->name('reservations.registration');
        Route::post('/checkin/{id}', [ReservationController::class, 'checkin'])->name('reservations.checkin');
        Route::get('/check-out', [ReservationController::class, 'checkoutPage'])->name('reservations.checkout.page');
        Route::get('/check-out/{id}', [ReservationController::class, 'checkout'])->name('reservations.checkout');
        Route::post('/check-out/{id}/process', [ReservationController::class, 'processCheckout'])->name('reservations.process-checkout');
        Route::post('/{id}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');
        Route::get('/history', [ReservationController::class, 'history'])->name('reservations.history');
        Route::get('/{id}/history', [ReservationController::class, 'moveToHistory'])->name('reservations.moveToHistory');
    });

    // --- HR & ACCESS CONTROL ---
    Route::resource('employees', EmployeeController::class);
    Route::resource('roles', RoleController::class);

    // --- FINANCE & REPORTS ---
    Route::get('/pembayaran', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/pembayaran/store', [PaymentController::class, 'store'])->name('payments.store');

    Route::prefix('transactions')->group(function () {
        Route::get('/export', [TransactionController::class, 'exportExcel'])->name('transactions.export');
        Route::delete('/bulk-delete', [TransactionController::class, 'bulkDelete'])->name('transactions.bulkDelete');
        Route::delete('/{id}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
    });

    Route::get('/reports/operasional', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/keuangan', [TransactionController::class, 'index'])->name('reports.financial');

    // --- SECURITY & DEVICE MONITORING ---
    Route::prefix('admin')->group(function () {
        // Monitor Perangkat
        Route::get('/devices', [DeviceController::class, 'index'])->name('admin.devices.index');
        Route::delete('/devices/{id}', [DeviceController::class, 'logoutDevice'])->name('admin.devices.logout');
        
        // Firewall / Blacklist
        Route::get('/blacklist', [DeviceController::class, 'blacklist'])->name('admin.ip.blacklist');
        Route::post('/ip-block', [DeviceController::class, 'blockIp'])->name('admin.ip.block');
        Route::delete('/blacklist/{id}', [DeviceController::class, 'unblockIp'])->name('admin.ip.unblock');
    });

    // --- ACCOUNT SETTINGS ---
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // --- UTILITY ---
    Route::get('/fix-data', function () {
        \App\Models\Reservation::whereNull('status')->update(['status' => 'booked']);
        return 'Data status reservasi berhasil dipulihkan.';
    });
});