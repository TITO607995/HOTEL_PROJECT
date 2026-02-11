<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssignRoleController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RoomController;
use App\Models\Room; 
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// --- DASHBOARD (Statistik Real-time) ---
Route::get('/dashboard', function () {
    // Menghitung SISA kamar yang tersedia saja
    $stats = [
        'Suite'    => Room::where('type', 'Suite')->where('status', 'available')->count(),
        'Standard' => Room::where('type', 'Standard')->where('status', 'available')->count(),
        'Deluxe'   => Room::where('type', 'Deluxe')->where('status', 'available')->count(),
    ];

    // Mapping data semua kamar untuk tabel
    $rooms = Room::all()->map(function($room) {
        $color = 'bg-gray-500'; // Default
        if ($room->status == 'available') $color = 'bg-green-500';
        if ($room->status == 'occupied')  $color = 'bg-red-600';
        if ($room->status == 'cleaning')  $color = 'bg-blue-400';

        return [
            'no'     => $room->room_number,
            'type'   => $room->type,
            'status' => $room->status,
            'color'  => $color
        ];
    });

    return view('dashboard', compact('stats', 'rooms'));
})->middleware(['auth', 'verified'])->name('dashboard');

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