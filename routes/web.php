<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SpvController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\ManageKaryawanController;
use App\Http\Controllers\ManageShiftController;

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

Route::get('/', function () {
    return redirect('/login');
});

// Route::middleware(['auth', 'admin'])->group(function () {
//     Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
// });

// Route::middleware(['auth', 'spv'])->group(function () {
//     Route::get('/spv/dashboard', [DashboardController::class, 'spv'])->name('spv.dashboard');
// });

// Route::middleware(['auth', 'karyawan'])->group(function () {
//     Route::get('/karyawan/dashboard', [DashboardController::class, 'karyawan'])->name('karyawan.dashboard');
// });

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

//dashboard route

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('dashboard.admin');
    Route::prefix('toko')->group(function () {
        Route::get('/', [ManageShiftController::class, 'index'])->name('shift.index');
        Route::get('/create', [ManageShiftController::class, 'create'])->name('shift.create');
        Route::post('/', [ManageShiftController::class, 'store'])->name('shift.store');
        Route::get('/{shift}/edit', [ManageShiftController::class, 'edit'])->name('shift.edit');
        Route::put('/{shift}', [ManageShiftController::class, 'update'])->name('shift.update');
        Route::delete('/{shift}', [ManageShiftController::class, 'destroy'])->name('shift.destroy');
    });
});

Route::middleware(['auth', 'role:spv'])->group(function () {
    Route::get('/spv/dashboard', [SpvController::class, 'index'])->name('dashboard.spv');
});

Route::middleware(['auth', 'role:karyawan'])->group(function () {
    Route::get('/karyawan/dashboard', [KaryawanController::class, 'index'])->name('dashboard.karyawan');
});

Route::middleware(['auth', 'role:admin|spv'])->group(function () {
    Route::prefix('manage-karyawan')->group(function () {
        Route::get('/', [ManageKaryawanController::class, 'index'])->name('manage-karyawan.index');
        Route::get('/create', [ManageKaryawanController::class, 'create'])->name('manage-karyawan.create');
        Route::post('/', [ManageKaryawanController::class, 'store'])->name('manage-karyawan.store');
        Route::get('/{karyawan}/edit', [ManageKaryawanController::class, 'edit'])->name('manage-karyawan.edit');
        Route::put('/{karyawan}', [ManageKaryawanController::class, 'update'])->name('manage-karyawan.update');
        Route::delete('/{karyawan}', [ManageKaryawanController::class, 'destroy'])->name('manage-karyawan.destroy');
    });
    Route::prefix('shift')->group(function () {
        Route::get('/', [ManageShiftController::class, 'index'])->name('shift.index');
        Route::get('/create', [ManageShiftController::class, 'create'])->name('shift.create');
        Route::post('/', [ManageShiftController::class, 'store'])->name('shift.store');
        Route::get('/{shift}/edit', [ManageShiftController::class, 'edit'])->name('shift.edit');
        Route::put('/{shift}', [ManageShiftController::class, 'update'])->name('shift.update');
        Route::delete('/{shift}', [ManageShiftController::class, 'destroy'])->name('shift.destroy');
    });
});
