<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Toko;
use App\Models\Shift;
use App\Models\Divisi;
use App\Models\LaporanMingguan;
use App\Models\JadwalKaryawan;
use App\Models\Libur;
use App\Models\Keuangan;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LaporanMingguanController extends Controller
{
    public function index(Request $request)
    {
        // $karyawans = User::where('status', 'aktif')->get();
        // $mingguans = LaporanMingguan::all();
        // $karyawans = User::all();
        $mingguKe = $request->query('minggu_ke', Carbon::today()->startOfWeek(Carbon::SATURDAY)->weekOfYear);
        // Hitung tanggal awal dan akhir dari minggu_ke
        $tahun = Carbon::now()->year;
        $startDate = Carbon::now()->setISODate($tahun, $mingguKe + 1)->startOfWeek(Carbon::SATURDAY);
        $endDate = $startDate->copy()->addDays(6);

        //------------------------------------------------
        $mingguans = LaporanMingguan::where('minggu_ke', $mingguKe)
            ->whereHas('users', function ($query) {
                $query->where('status', 'aktif');
            })
            ->with(['users' => function ($query) {
                $query->where('status', 'aktif');
            }])
            ->get();

        $karyawans = User::where('status', 'aktif')->get();
        // $mingguan = User::orderBy('status', 'asc')->orderBy('id', 'asc')->get();
        return view('mingguan_view.index', compact('mingguans', 'mingguKe', 'startDate', 'endDate'));
    }

    public function view(Request $request)
    {
        $user = auth()->user();
        // $karyawans = User::where('status', 'aktif')->get();
        // $mingguans = LaporanMingguan::all();
        // $karyawans = User::all();
        $mingguKe = $request->query('minggu_ke', Carbon::today()->startOfWeek(Carbon::SATURDAY)->weekOfYear);
        // Hitung tanggal awal dan akhir dari minggu_ke
        $tahun = Carbon::now()->year;
        $startDate = Carbon::now()->setISODate($tahun, $mingguKe + 1)->startOfWeek(Carbon::SATURDAY);
        $endDate = $startDate->copy()->addDays(6);

        //------------------------------------------------
        $mingguans = LaporanMingguan::where('minggu_ke', $mingguKe)
            ->whereHas('users', function ($query) use ($user) {
                $query->where('id', $user->id)->where('status', 'aktif');
            })
            ->with(['users' => function ($query) use ($user) {
                $query->where('id', $user->id)->where('status', 'aktif');
            }])
            ->get();

        // $mingguans = LaporanMingguan::where('minggu_ke', $mingguKe)
        //     ->whereHas('users', function ($query) {
        //         $query->where('status', 'aktif');
        //     })
        //     ->with(['users' => function ($query) {
        //         $query->where('status', 'aktif');
        //     }])
        //     ->get();

        $karyawans = User::where('status', 'aktif')->get();
        // $mingguan = User::orderBy('status', 'asc')->orderBy('id', 'asc')->get();
        return view('mingguan_view.view-mingguan', compact('mingguans', 'mingguKe', 'startDate', 'endDate'));
    }


    public function generateLaporanMingguanForAll($mingguKe)
    {
        // Ambil semua user yang memiliki jadwal karyawan untuk minggu ke tertentu
        $jadwalKaryawan = JadwalKaryawan::where('minggu_ke', $mingguKe)
            ->get()
            ->groupBy('user_id'); // Kelompokkan berdasarkan user_id

        // Proses setiap user untuk menghasilkan laporan mingguan
        foreach ($jadwalKaryawan as $userId => $jadwals) {
            $user = $jadwals->first()->users ?? null;

            // Skip jika user tidak ada atau statusnya bukan 'aktif'
            if (!$user || $user->status !== 'aktif') {
                continue;
            }
            // Inisialisasi array untuk menyimpan hari-hari dalam minggu
            $hari = [
                'd1'  => null,
                'd2' => null,
                'd3'  => null,
                'd4' => null,
                'd5'   => null,
                'd6'  => null,
                'd7'  => null,
            ];

            $row = 7;
            $mingguan = 0;
            $tottelat = 0;
            $kedatangan = 0;
            $totlembur = 0;
            $status = 'selesai';
            $tnull = 0;
            $jumlahBonusMingguan = 0;
            $db_keuangan = Keuangan::first();

            // Proses setiap jadwal karyawan untuk user ini
            foreach ($jadwals as $jadwalKaryawan) {
                // Ambil hari dalam minggu dari tanggal
                $tanggal = Carbon::parse($jadwalKaryawan->tanggal);
                $dayOfWeek = $tanggal->dayOfWeek; // 0 = Minggu, 1 = Senin, ..., 6 = Sabtu

                $shift = $jadwalKaryawan->shift->nama_shift;
                $jamMasuk = $jadwalKaryawan->absensi->jam_masuk ?? '-';
                $isLibur = Libur::isLibur($tanggal);
                $keteranganLibur = $isLibur ? Libur::getLibur($tanggal)->keterangan : null;

                $value = json_encode([
                    'shift' => $shift,
                    'jam_masuk' => $jamMasuk,
                    'libur' => $isLibur,
                    'keterangan_libur' => $keteranganLibur
                ]);

                // Tentukan hari sesuai dengan dayOfWeek
                switch ($dayOfWeek) {
                    case 0:
                        $hari['d2'] = $value;
                        break;
                    case 1:
                        $hari['d3'] = $value;
                        break;
                    case 2:
                        $hari['d4'] = $value;
                        break;
                    case 3:
                        $hari['d5'] = $value;
                        break;
                    case 4:
                        $hari['d6'] = $value;
                        break;
                    case 5:
                        $hari['d7'] = $value;
                        break;
                    case 6:
                        $hari['d1'] = $value;
                        break;
                }

                $isShiftLibur = stripos($jadwalKaryawan->shift->nama_shift, 'Libur') !== false;
                $isShiftCuti  = stripos($jadwalKaryawan->shift->nama_shift, 'Cuti') !== false;
                $isShiftSakit  = stripos($jadwalKaryawan->shift->nama_shift, 'Sakit') !== false;
                $totalCuti    = $jadwalKaryawan->users->total_cuti;
                $isShiftLiburPG = stripos($jadwalKaryawan->shift->nama_shift, 'Libur Pengganti') !== false;

                if ($jumlahBonusMingguan < 6) {
                    if (
                        ($jadwalKaryawan->cek_keterlambatan == 0) ||
                        ($isShiftCuti && $totalCuti > 0) ||
                        $isLibur || ($jadwalKaryawan->users->divisi->kedatangan == false || $isShiftLiburPG)
                    ) {
                        $mingguan += $db_keuangan->uang_mingguan;
                        $jumlahBonusMingguan++;
                    }
                    // elseif ($jadwalKaryawan->cek_keterlambatan == 1) {
                    //     $jumlahBonusMingguan++;
                    // }
                }
                // if (($jumlahBonusMingguan >= 6) && ($jadwalKaryawan->cek_keterlambatan == 1)) {
                //     $mingguan -= $db_keuangan->uang_mingguan;
                // }

                // Cek keterlambatan dan absensi
                if ($jadwalKaryawan->cek_keterlambatan == 2) {
                    if ($isShiftCuti || ($isLibur) || $isShiftSakit ||  $isShiftLibur || ($jadwalKaryawan->users->divisi->kedatangan == false)) {
                        $status = 'selesai';
                    } else {
                        // $status = 'kurang';
                        $tnull++;
                    }
                } elseif ($jadwalKaryawan->cek_keterlambatan == 0 && !$jadwalKaryawan->absensi && !$isShiftCuti && !$isShiftLibur && !$isShiftSakit) {
                    $status = 'kurang';
                } elseif ($jadwalKaryawan->cek_keterlambatan == 1) {
                    $tottelat++;
                    // $tnull++;
                }
                $totlembur = $totlembur + $jadwalKaryawan->total_lembur;
            }

            if ($tnull > 0) {
                $status = 'kurang';
            }
            if ($jumlahBonusMingguan >= 6 && ($tottelat > 0 || $tnull > 0)) {
                $mingguan -= $db_keuangan->uang_mingguan;
            }

            if ($status == 'kurang' || $tottelat > 0 || (count($jadwals) < 7)) {
                $kedatangan = 0;
            } else {
                if ($jadwalKaryawan->users->divisi->kedatangan == true) {
                    // if ($jadwalKaryawan->users->total_cuti > 0) 
                    $kedatangan = $db_keuangan->uang_kedatangan;
                }
                // $status = 'selesai';
            }

            LaporanMingguan::updateOrCreate(
                ['user_id' => $userId, 'minggu_ke' => $mingguKe],
                [
                    'd1' => $hari['d1'],
                    'd2' => $hari['d2'],
                    'd3' => $hari['d3'],
                    'd4' => $hari['d4'],
                    'd5' => $hari['d5'],
                    'd6' => $hari['d6'],
                    'd7' => $hari['d7'],
                    'uang_mingguan' => $mingguan,
                    'uang_kedatangan' => $kedatangan,
                    'uang_lembur_mingguan' => $totlembur,
                    'status' => $status,
                ]
            );


            // $existingSchedule = LaporanMingguan::where('user_id', $userId)
            //     ->where('minggu_ke', $mingguKe)
            //     ->first();

            // // JIKA JADWAL SUDAH ADA MAKA UPDATE
            // if ($existingSchedule) {
            //     // Jika jadwal sudah ada, update jadwal yang ada
            //     $existingSchedule->update([
            //         'user_id' => $userId,
            //         'minggu_ke' => $mingguKe,
            //         'd1' => $hari['d1'],
            //         'd2' => $hari['d2'],
            //         'd3' => $hari['d3'],
            //         'd4' => $hari['d4'],
            //         'd5' => $hari['d5'],
            //         'd6' => $hari['d6'],
            //         'd7' => $hari['d7'],
            //         'uang_mingguan' => $mingguan,  // Sementara kosongkan
            //         'uang_kedatangan' => $kedatangan,  // Sementara kosongkan
            //         'uang_lembur_mingguan' => $totlembur,  // Sementara kosongkan
            //         'status' => $status,  // Sementara kosongkan
            //     ]);
            // } else {
            //     // Simpan laporan mingguan untuk user
            //     $laporanMingguan = LaporanMingguan::create([
            //         'user_id' => $userId,
            //         'minggu_ke' => $mingguKe,
            //         'd1' => $hari['d1'],
            //         'd2' => $hari['d2'],
            //         'd3' => $hari['d3'],
            //         'd4' => $hari['d4'],
            //         'd5' => $hari['d5'],
            //         'd6' => $hari['d6'],
            //         'd7' => $hari['d7'],
            //         'uang_mingguan' => $mingguan,  // Sementara kosongkan
            //         'uang_kedatangan' => $kedatangan,  // Sementara kosongkan
            //         'uang_lembur_mingguan' => $totlembur,  // Sementara kosongkan
            //         'status' => $status,  // Sementara kosongkan
            //     ]);
            // }
        }

        // return response()->json([
        //     'message' => 'Laporan mingguan berhasil dibuat untuk seluruh karyawan.',
        // ]);
        return redirect()->back()->with('success', 'Jadwal berhasil diimpor.');
    }
}
