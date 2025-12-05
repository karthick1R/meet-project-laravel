<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\ProductKeyController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ProfileController;

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

// Product Key Routes removed - payment functionality removed

// Auth Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Public routes
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Rooms
    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::get('/rooms/{id}', [RoomController::class, 'show'])->name('rooms.show');
    Route::get('/rooms/{id}/availability', [RoomController::class, 'availability'])->name('rooms.availability');

    // Bookings
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/overall-bookings', [BookingController::class, 'bookings'])->name('bookings.overall');
    Route::get('/bookings/{id}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    
    // AJAX endpoints for bookings
    Route::get('/api/bookings/calendar', [BookingController::class, 'calendar'])->name('bookings.calendar');
    Route::post('/api/bookings/check-availability', [BookingController::class, 'checkAvailability'])->name('bookings.check-availability');

    // Admin routes
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/rooms', [AdminController::class, 'rooms'])->name('rooms.index');
        Route::post('/rooms', [AdminController::class, 'storeRoom'])->name('rooms.store');
        Route::put('/rooms/{id}', [AdminController::class, 'updateRoom'])->name('rooms.update');
        Route::delete('/rooms/{id}', [AdminController::class, 'deleteRoom'])->name('rooms.delete');
        
        Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings.index');
        Route::post('/block-time-slot', [AdminController::class, 'blockTimeSlot'])->name('block-time-slot');
        
        // User Management
                        Route::get('/users', [AdminController::class, 'users'])->name('users.index');
        Route::get('/users/{id}', [AdminController::class, 'getUser'])->name('users.show');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');
    });
});
