<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssignRoleController;
use App\Http\Controllers\ReservationController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::get('/assign-role', [AssignRoleController::class, 'index'])->name('assign-role.index');
    Route::get('/assign-role/{user}/edit', [AssignRoleController::class, 'edit'])->name('assign-role.edit');
    Route::put('/assign-role/{user}', [AssignRoleController::class, 'update'])->name('assign-role.update');
    Route::get('/reservasi', [ReservationController::class, 'index'])->name('reservations.index');
Route::post('/reservasi', [ReservationController::class, 'store'])->name('reservations.store');
});

require __DIR__.'/auth.php';
