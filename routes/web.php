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
use App\Http\Controllers\ManageDivisiController;
use App\Http\Controllers\ManageLiburController;
use App\Http\Controllers\ManageLemburController;
use App\Http\Controllers\InputJadwalKaryawanController;
use App\Http\Controllers\LaporanMingguanController;
use App\Http\Controllers\PengajuanCutiController;
use Illuminate\Support\Facades\Artisan;

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
        Route::post('/import', [ManageAbsensiController::class, 'import'])->name('absensi.import');
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
    //DIVISI
    Route::prefix('divisi')->group(function () {
        Route::get('/', [ManageDivisiController::class, 'index'])->name('divisi.index');
        Route::get('/create', [ManageDivisiController::class, 'create'])->name('divisi.create');
        Route::post('/', [ManageDivisiController::class, 'store'])->name('divisi.store');
        Route::get('/{divisi}/edit', [ManageDivisiController::class, 'edit'])->name('divisi.edit');
        Route::put('/{divisi}', [ManageDivisiController::class, 'update'])->name('divisi.update');
        Route::delete('/{divisi}', [ManageDivisiController::class, 'destroy'])->name('divisi.destroy');
    });
    //LIBUR
    Route::prefix('libur')->group(function () {
        Route::get('/', [ManageLiburController::class, 'index'])->name('libur.index');
        Route::get('/create', [ManageLiburController::class, 'create'])->name('libur.create');
        Route::post('/', [ManageLiburController::class, 'store'])->name('libur.store');
        Route::get('/{libur}/edit', [ManageLiburController::class, 'edit'])->name('libur.edit');
        Route::put('/{libur}', [ManageLiburController::class, 'update'])->name('libur.update');
        Route::delete('/{libur}', [ManageLiburController::class, 'destroy'])->name('libur.destroy');
        Route::post('/import', [ManageLiburController::class, 'import'])->name('libur.import');
    });
    //MINGGUAN
    Route::prefix('mingguan')->group(function () {
        Route::get('/', [LaporanMingguanController::class, 'index'])->name('mingguan.index');
        Route::get('/export', [InputJadwalKaryawanController::class, 'export'])->name('input-jadwal.export');
        Route::get('/export_all/{week}', [InputJadwalKaryawanController::class, 'export_all'])->name('input-jadwal.export-all');
        Route::get('/laporan-mingguan/{week}', [LaporanMingguanController::class, 'generateLaporanMingguanForAll'])->name('mingguan.generateLaporanMingguanForAll');
        // Route::get('/create', [LaporanMingguanController::class, 'create'])->name('mingguan.create');
        // Route::post('/', [LaporanMingguanController::class, 'store'])->name('mingguan.store');
        // Route::get('/{lembur}/edit', [LaporanMingguanController::class, 'edit'])->name('mingguan.edit');
        // Route::put('/{lembur}', [LaporanMingguanController::class, 'update'])->name('mingguan.update');
        // Route::delete('/{lembur}', [LaporanMingguanController::class, 'destroy'])->name('mingguan.destroy');
    });

    Route::post('/generate-jadwal-admin', [InputJadwalKaryawanController::class, 'generateBulananAdmin'])->name('generate.jadwal.admin');
});

Route::middleware(['auth', 'role:spv'])->group(function () {
    Route::get('/spv/dashboard', [SpvController::class, 'index'])->name('dashboard.spv');
});

Route::middleware(['auth', 'role:karyawan'])->group(function () {
    Route::get('/karyawan/dashboard', [KaryawanController::class, 'index'])->name('dashboard.karyawan');
});

Route::middleware(['auth', 'role:admin|spv'])->group(function () {
    Route::get('/calendar', [InputJadwalKaryawanController::class, 'calendar'])->name('calendar');
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
        // Route::get('/import', [InputJadwalKaryawanController::class, 'import'])->name('input-jadwal.import');
        Route::post('/import', [InputJadwalKaryawanController::class, 'import'])->name('input-jadwal.import');
        Route::post('/', [InputJadwalKaryawanController::class, 'store'])->name('input-jadwal.store');
        Route::get('/{input_jadwal}/edit', [InputJadwalKaryawanController::class, 'edit'])->name('input-jadwal.edit');
        Route::put('/{input_jadwal}', [InputJadwalKaryawanController::class, 'update'])->name('input-jadwal.update');
        Route::delete('/{input_jadwal}', [InputJadwalKaryawanController::class, 'destroy'])->name('input-jadwal.destroy');
    });
    // PENGAJUAN CUTI
    Route::prefix('cuti')->group(function () {
        // Route::get('/', [PengajuanCutiController::class, 'index'])->name('cuti.index');
        Route::get('/create', [PengajuanCutiController::class, 'create'])->name('cuti.create');
        Route::post('/store', [PengajuanCutiController::class, 'store'])->name('cuti.store');
        Route::get('/approval', [PengajuanCutiController::class, 'approvalIndex'])->name('cuti.approval.index');
        Route::post('/{id}/approve', [PengajuanCutiController::class, 'approve'])->name('cuti.approve');
        Route::post('/{id}/spv_approve', [PengajuanCutiController::class, 'spvapprove'])->name('cuti.spv_approve');
        Route::post('/{id}/reject', [PengajuanCutiController::class, 'reject'])->name('cuti.reject');
        Route::post('/{id}/cancel', [PengajuanCutiController::class, 'cancel'])->name('cuti.cancel');
    });
    //LEMBUR
    Route::prefix('lembur')->group(function () {
        Route::get('/manage', [ManageLemburController::class, 'index'])->name('lembur.index');
        Route::get('/create', [ManageLemburController::class, 'create'])->name('lembur.create');
        Route::post('/', [ManageLemburController::class, 'store'])->name('lembur.store');
        Route::get('/{lembur}/edit', [ManageLemburController::class, 'edit'])->name('lembur.edit');
        Route::put('/{lembur}', [ManageLemburController::class, 'update'])->name('lembur.update');
        Route::delete('/{lembur}', [ManageLemburController::class, 'destroy'])->name('lembur.destroy');
        Route::get('/', [ManageLemburController::class, 'import'])->name('lembur.import');
        Route::post('/import', [InputJadwalKaryawanController::class, 'importLembur'])->name('lembur.import-lembur');
        Route::get('/detail', [ManageLemburController::class, 'getDetail'])->name('jadwal.detail.ajax');
    });

    // GENERATE JADWAL
    Route::get('/generate-jadwal', [InputJadwalKaryawanController::class, 'generate'])->name('generate.jadwal');
    Route::post('/generate-jadwal-bulanan', [InputJadwalKaryawanController::class, 'generateBulanan'])->name('generate.jadwal.bulanan');
    Route::get('/export-template', [App\Http\Controllers\ExportController::class, 'exportTemplate'])->name('export-template');
    Route::get('/export-template-lembur', [App\Http\Controllers\ExportController::class, 'exportTemplateLembur'])->name('export-template-lembur');
    Route::get('/export-template-libur', [App\Http\Controllers\ExportController::class, 'exportTemplateLibur'])->name('export-template-libur');
});

Route::middleware(['auth', 'role:admin|spv|karyawan'])->group(function () {
    Route::get('/jadwal', [InputJadwalKaryawanController::class, 'read'])->name('input-jadwal.read');
    Route::get('/cuti', [PengajuanCutiController::class, 'index'])->name('cuti.index');
});

Route::get('/run-migrate-1234', function () {
    Artisan::call('migrate:fresh');
    return 'Migration dijalankan!';
});

Route::get('/run-seed-1234', function () {
    Artisan::call('db:seed');
    return 'Seeder dijalankan!';
});

Route::get('/link-storage-1234', function () {
    Artisan::call('storage:link');
    return 'Storage link berhasil dibuat!';
});
