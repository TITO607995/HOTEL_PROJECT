<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssignRoleController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\GuestController;
use App\Models\Room; 
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});


Route::get('/dashboard', function () {
    $stats = [
        'Standard' => \App\Models\Room::where('type', 'Standard')->count(),
        'Deluxe'   => \App\Models\Room::where('type', 'Deluxe')->count(),
        'Suite'    => \App\Models\Room::where('type', 'Suite')->count(),
    ];

    $roomList = \App\Models\Room::with(['reservations' => function($q) {
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
                'no'        => $room->room_number,
                'type'      => $room->type,
                'left_status'=> $leftStatus,
                'payment'   => $paymentMethod,
                'is_paid'   => $isPaid,
                'action'    => strtoupper($room->status),
                'action_color' => ($room->status == 'occupied') ? 'bg-red-500' : (($room->status == 'vacant dirty') ? 'bg-yellow-500' : 'bg-orange-500'),
                'visibility' => ($latestRes && $latestRes->is_incognito) ? 'Incognito' : 'Public'
            ];
        });

    return view('dashboard', compact('stats', 'roomList'));
})->middleware(['auth'])->name('dashboard');

Route::get('/check-out', [ReservationController::class, 'checkoutPage'])->name('reservations.checkout.page');
Route::post('/check-out/{id}', [ReservationController::class, 'processCheckout'])->name('reservations.checkout.process');
Route::get('/guests', [ReservationController::class, 'guestIndex'])->name('guests.index');
Route::post('/guests/incognito/{id}', [ReservationController::class, 'toggleIncognito'])->name('guests.toggle-incognito');
Route::post('/reservasi/extend/{id}', [ReservationController::class, 'extend'])->name('reservations.extend');
Route::get('/rooms/maintenance', [RoomController::class, 'maintenancePage'])->name('rooms.maintenance.page');
Route::post('/rooms/maintenance/{id}', [RoomController::class, 'updateMaintenance'])->name('rooms.maintenance.update');

// --- SEMUA ROUTE YANG BUTUH LOGIN ---
Route::middleware('auth')->group(function () {
    
    // ROOMS MANAGEMENT
    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::post('/rooms/store', [RoomController::class, 'store'])->name('room.store');

    // RESERVASI
    Route::get('/reservasi', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservasi/tambah', [ReservationController::class, 'create'])->name('reservations.create');
    Route::post('/reservasi/simpan', [ReservationController::class, 'store'])->name('reservations.store');

    // REGISTRATION & CHECK-IN
    Route::get('/registration', [ReservationController::class, 'registration'])->name('reservations.registration');
    Route::post('/checkin/{id}', [ReservationController::class, 'checkin'])->name('reservations.checkin');

    // USER PROFILE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //TAMU WEB

    // --- KHUSUS SUPERADMIN ---
    Route::middleware('can:superadmin-only')->group(function () {
        Route::resource('roles', RoleController::class);
        Route::resource('users', UserController::class);
        
        // Assign Role Manual
        Route::get('/assign-role', [AssignRoleController::class, 'index'])->name('assign-role.index');
        Route::get('/assign-role/{user}/edit', [AssignRoleController::class, 'edit'])->name('assign-role.edit');
        Route::put('/assign-role/{user}', [AssignRoleController::class, 'update'])->name('assign-role.update');
    });
});

require __DIR__.'/auth.php';