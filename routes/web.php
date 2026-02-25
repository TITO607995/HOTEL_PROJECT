<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\GuestController;
use Illuminate\Support\Facades\Route;
use App\Models\Room;

// 1. REDIRECT AWAL
Route::get('/', function () {
    return redirect()->route('login');
});
// 2. SEMUA ROUTE YANG WAJIB LOGIN
Route::middleware(['auth'])->group(function () {

    // --- DASHBOARD ---
    Route::get('/dashboard', function () {
        $stats = [
            'Standard' => Room::where('type', 'Standard')->count(),
            'Deluxe'   => Room::where('type', 'Deluxe')->count(),
            'Suite'    => Room::where('type', 'Suite')->count(),
        ];

        $roomList = Room::with(['reservations' => function($q) {
                $q->latest();
            }])
            ->where('status', '!=', 'available')
            ->get()
            ->map(function($room) {
                $latestRes = $room->reservations->first();
                $leftStatus = ($room->status == 'vacant dirty') ? 'Dirty' : 'In-house';
                $paymentMethod = $latestRes ? $latestRes->payment_method : '-';
                $isPaid = $latestRes && $latestRes->reservation_type == 'guaranteed'; 

                return [
                    'no'           => $room->room_number,
                    'type'         => $room->type,
                    'left_status'  => $leftStatus,
                    'payment'      => $paymentMethod,
                    'is_paid'      => $isPaid,
                    'action'       => strtoupper($room->status),
                    'action_color' => ($room->status == 'occupied') ? 'bg-red-500' : (($room->status == 'vacant dirty') ? 'bg-yellow-500' : 'bg-orange-500'),
                    'visibility'   => ($latestRes && $latestRes->is_incognito) ? 'Incognito' : 'Public'
                ];
            });

        return view('dashboard', compact('stats', 'roomList'));
    })->name('dashboard');

    // --- MANAJEMEN KAMAR ---
    Route::prefix('rooms')->group(function () {
        Route::get('/', [RoomController::class, 'index'])->name('rooms.index');
        Route::post('/store', [RoomController::class, 'store'])->name('room.store');
        
        // Maintenance (OO/OS)
        Route::get('/maintenance', [RoomController::class, 'maintenancePage'])->name('rooms.maintenance.page');
        Route::post('/maintenance/{id}', [RoomController::class, 'updateMaintenance'])->name('rooms.maintenance.update');
    });

    Route::get('/fix-data', function() {
        // Ubah semua reservasi yang belum check-out menjadi 'booked'
        \App\Models\Reservation::whereNull('status')->update(['status' => 'booked']);
        return "Data berhasil diperbaiki! Silakan buka halaman Check-in.";
    });

    // --- RESERVASI & TAMU ---
   // --- MANAJEMEN TAMU (GUESTS) ---
    Route::prefix('guests')->group(function () {
        Route::get('/', [ReservationController::class, 'guestIndex'])->name('guests.index');
        
        // Letakkan rute bulk-delete DI ATAS rute {id} agar tidak bentrok
        Route::delete('/bulk-delete', [ReservationController::class, 'bulkDelete'])->name('guests.bulk-delete'); 
        
        Route::post('/incognito/{id}', [ReservationController::class, 'toggleIncognito'])->name('guests.toggle-incognito');
        Route::put('/{id}', [ReservationController::class, 'updateGuest'])->name('guests.update');
        Route::delete('/{id}', [ReservationController::class, 'destroyGuest'])->name('guests.destroy');
    });

    Route::prefix('reservasi')->group(function () {
        Route::get('/', [ReservationController::class, 'index'])->name('reservations.index');
        Route::get('/tambah', [ReservationController::class, 'create'])->name('reservations.create');
        Route::post('/simpan', [ReservationController::class, 'store'])->name('reservations.store');
        Route::post('/extend/{id}', [ReservationController::class, 'extend'])->name('reservations.extend');
        
        // Registration & Check-in
        Route::get('/registration', [ReservationController::class, 'registration'])->name('reservations.registration');
        Route::post('/checkin/{id}', [ReservationController::class, 'checkin'])->name('reservations.checkin');
        
        // Check-out
        Route::get('/check-out', [ReservationController::class, 'checkoutPage'])->name('reservations.checkout.page');
        Route::get('/check-out/{id}', [App\Http\Controllers\ReservationController::class, 'checkout'])->name('reservations.checkout');
        Route::post('/check-out/{id}/process', [App\Http\Controllers\ReservationController::class, 'processCheckout'])->name('reservations.process-checkout');
        Route::post('/reservasi/check-out/{id}/process', [GuestController::class, 'processCheckout'])->name('reservasi.processCheckout');
    });

    Route::resource('employees', EmployeeController::class);

    Route::resource('roles', RoleController::class);

    Route::get('/reports/operasional', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/keuangan', [ReportController::class, 'financial'])->name('reports.financial'); 
    Route::delete('/financial-reports/{id}', [ReservationController::class, 'destroyTransaction'])->name('transactions.destroy');
    Route::prefix('employees')->group(function () {
        Route::get('/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/store', [EmployeeController::class, 'store'])->name('employees.store');
    });

    // --- PROFILE USER ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::delete('/transactions/bulk-delete', [TransactionController::class, 'bulkDelete'])
    ->name('transactions.bulkDelete');

    Route::get('/pembayaran', [PaymentController::class, 'index'])->name('payments.index');

// Tambahkan juga untuk simpan pembayarannya nanti
Route::post('/pembayaran/store', [PaymentController::class, 'store'])->name('payments.store');
Route::delete('/guests/bulk-delete', [GuestController::class, 'bulkDelete'])->name('guests.bulk-delete');

Route::post('/rooms/store', [RoomController::class, 'store'])->name('rooms.store');

// Route untuk memproses perpanjangan tanggal
Route::post('/reservasi/perpanjang/{id}', [ReservationController::class, 'extend'])->name('reservations.extend');
});

require __DIR__.'/auth.php';