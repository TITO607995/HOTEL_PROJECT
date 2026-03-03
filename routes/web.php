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
    GuestController,
    DashboardController,
    DeviceController
};
use App\Http\Controllers\Auth\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| REDIRECT AWAL & AUTH
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('throttle:login')
    ->name('login');

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| ROUTE YANG WAJIB LOGIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/about', function () {
        return view('about.about');
    })->name('about.about');

    // ROOMS
    Route::prefix('rooms')->group(function () {
        Route::get('/', [RoomController::class, 'index'])->name('rooms.index');
        Route::post('/store', [RoomController::class, 'store'])->name('rooms.store');
        Route::get('/maintenance', [RoomController::class, 'maintenancePage'])->name('rooms.maintenance');
        Route::post('/maintenance/{id}/update', [RoomController::class, 'updateMaintenance'])->name('rooms.maintenance.update');
    });

    // GUESTS
    Route::prefix('guests')->group(function () {
        Route::get('/', [ReservationController::class, 'guestIndex'])->name('guests.index');
        Route::delete('/bulk-delete', [ReservationController::class, 'bulkDelete'])->name('guests.bulk-delete');
        Route::post('/incognito/{id}', [ReservationController::class, 'toggleIncognito'])->name('guests.toggle-incognito');
        Route::put('/{id}', [ReservationController::class, 'updateGuest'])->name('guests.update');
        Route::delete('/{id}', [ReservationController::class, 'destroyGuest'])->name('guests.destroy');
    });

    // RESERVASI
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
    });

    // EMPLOYEE & ROLE
    Route::resource('employees', EmployeeController::class);
    Route::resource('roles', RoleController::class);

    // PAYMENT & TRANSACTION
    Route::get('/pembayaran', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/pembayaran/store', [PaymentController::class, 'store'])->name('payments.store');

    // KEUANGAN (TRANSACTION)
    Route::prefix('transactions')->group(function () {
        Route::get('/export', [TransactionController::class, 'exportExcel'])->name('transactions.export');
        Route::delete('/bulk-delete', [TransactionController::class, 'bulkDelete'])->name('transactions.bulkDelete');
        Route::delete('/{id}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
    });

    // REPORTS
    Route::get('/reports/operasional', [ReportController::class, 'index'])->name('reports.index');
    // Diarahkan ke TransactionController agar sinkron dengan fungsi hapus/ekspor
    Route::get('/reports/keuangan', [TransactionController::class, 'index'])->name('reports.financial');

    // PROFILE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // DEVICE MONITOR
    Route::get('/devices-monitor', [DeviceController::class, 'index'])->name('admin.devices');
    Route::delete('/devices-monitor/{id}', [DeviceController::class, 'logoutDevice'])->name('admin.devices.logout');

    // UTILITY / FIX
    Route::get('/fix-data', function () {
        \App\Models\Reservation::whereNull('status')->update(['status' => 'booked']);
        return 'Data berhasil diperbaiki';
    });
    Route::delete('/guests/bulk-delete', [GuestController::class, 'bulkDelete'])->name('guests.bulk-delete');
    Route::get('/reservations/history', [ReservationController::class, 'history'])->name('reservations.history');
    // Tambahkan ini di routes/web.php
Route::get('/reservations/{id}/history', [ReservationController::class, 'moveToHistory'])->name('reservations.moveToHistory');
    });