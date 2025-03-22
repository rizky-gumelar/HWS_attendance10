<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\JenisCutiController;
use App\Http\Controllers\SpvController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\ManageKaryawanController;
use App\Http\Controllers\ManageShiftController;
use App\Http\Controllers\ManageTokoController;
use App\Http\Controllers\ManageAbsensiController;
use App\Http\Controllers\InputJadwalKaryawanController;

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

// Route::get('/{role}/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

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
    // TOKO
    Route::prefix('toko')->group(function () {
        Route::get('/', [ManageTokoController::class, 'index'])->name('toko.index');
        Route::get('/create', [ManageTokoController::class, 'create'])->name('toko.create');
        Route::post('/', [ManageTokoController::class, 'store'])->name('toko.store');
        Route::get('/{toko}/edit', [ManageTokoController::class, 'edit'])->name('toko.edit');
        Route::put('/{toko}', [ManageTokoController::class, 'update'])->name('toko.update');
        Route::delete('/{toko}', [ManageTokoController::class, 'destroy'])->name('toko.destroy');
    });
    // ABSENSI
    Route::prefix('absensi')->group(function () {
        Route::get('/', [ManageAbsensiController::class, 'index'])->name('absensi.index');
        Route::get('/create', [ManageAbsensiController::class, 'create'])->name('absensi.create');
        Route::post('/', [ManageAbsensiController::class, 'store'])->name('absensi.store');
        Route::get('/{absensi}/edit', [ManageAbsensiController::class, 'edit'])->name('absensi.edit');
        Route::put('/{absensi}', [ManageAbsensiController::class, 'update'])->name('absensi.update');
        Route::delete('/{absensi}', [ManageAbsensiController::class, 'destroy'])->name('absensi.destroy');
    });
    // JENIS CUTI
    Route::prefix('jenis-cuti')->group(function () {
        Route::get('/', [JenisCutiController::class, 'index'])->name('jenis-cuti.index');
        Route::get('/create', [JenisCutiController::class, 'create'])->name('jenis-cuti.create');
        Route::post('/', [JenisCutiController::class, 'store'])->name('jenis-cuti.store');
        Route::get('/{jenis-cuti}/edit', [JenisCutiController::class, 'edit'])->name('jenis-cuti.edit');
        Route::put('/{jenis-cuti}', [JenisCutiController::class, 'update'])->name('jenis-cuti.update');
        Route::delete('/{jenis-cuti}', [JenisCutiController::class, 'destroy'])->name('jenis-cuti.destroy');
    });
});

Route::middleware(['auth', 'role:spv'])->group(function () {
    Route::get('/spv/dashboard', [SpvController::class, 'index'])->name('dashboard.spv');
});

Route::middleware(['auth', 'role:karyawan'])->group(function () {
    Route::get('/karyawan/dashboard', [KaryawanController::class, 'index'])->name('dashboard.karyawan');
});

Route::middleware(['auth', 'role:admin|spv'])->group(function () {
    // CRUD KARYAWAN
    Route::prefix('manage-karyawan')->group(function () {
        Route::get('/', [ManageKaryawanController::class, 'index'])->name('manage-karyawan.index');
        Route::get('/create', [ManageKaryawanController::class, 'create'])->name('manage-karyawan.create');
        Route::post('/', [ManageKaryawanController::class, 'store'])->name('manage-karyawan.store');
        Route::get('/{karyawan}/edit', [ManageKaryawanController::class, 'edit'])->name('manage-karyawan.edit');
        Route::put('/{karyawan}', [ManageKaryawanController::class, 'update'])->name('manage-karyawan.update');
        Route::delete('/{karyawan}', [ManageKaryawanController::class, 'destroy'])->name('manage-karyawan.destroy');
    });
    // CRUD SHIFT
    Route::prefix('shift')->group(function () {
        Route::get('/', [ManageShiftController::class, 'index'])->name('shift.index');
        Route::get('/create', [ManageShiftController::class, 'create'])->name('shift.create');
        Route::post('/', [ManageShiftController::class, 'store'])->name('shift.store');
        Route::get('/{shift}/edit', [ManageShiftController::class, 'edit'])->name('shift.edit');
        Route::put('/{shift}', [ManageShiftController::class, 'update'])->name('shift.update');
        Route::delete('/{shift}', [ManageShiftController::class, 'destroy'])->name('shift.destroy');
    });
    // INPUT JADWAL KARYAWAN
    Route::prefix('input-jadwal')->group(function () {
        Route::get('/', [InputJadwalKaryawanController::class, 'index'])->name('input-jadwal.index');
        Route::get('/create', [InputJadwalKaryawanController::class, 'create'])->name('input-jadwal.create');
        Route::post('/', [InputJadwalKaryawanController::class, 'store'])->name('input-jadwal.store');
        Route::get('/{input_jadwal}/edit', [InputJadwalKaryawanController::class, 'edit'])->name('input-jadwal.edit');
        Route::put('/{input_jadwal}', [InputJadwalKaryawanController::class, 'update'])->name('input-jadwal.update');
        Route::delete('/{input-jadwal}', [InputJadwalKaryawanController::class, 'destroy'])->name('input-jadwal.destroy');
    });
});
