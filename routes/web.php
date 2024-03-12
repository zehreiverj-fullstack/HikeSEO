<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\SlotsController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// require __DIR__.'/auth.php';

Route::get('/', [SlotsController::class, 'index'])->name('slots.index');
Route::post('/book', [SlotsController::class, 'book'])->name('slots.book');
Route::get('{bookingId}/confirmation/{userId}', [SlotsController::class, 'confirmation'])->name('slots.confirmation');

Route::controller(AdminController::class)->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/', 'index')->name('admin.index');
        Route::get('/upcoming', 'getUpcomingBookings')->name('admin.upcoming');
        Route::get('/pending', 'getPendingBookings')->name('admin.pending');
        Route::post('/disable', 'disableDate')->name('admin.disable');
        Route::post('/approve', 'approveBooking')->name('admin.approve');
        Route::post('/register', 'register')->name('admin.register');
    });
});
