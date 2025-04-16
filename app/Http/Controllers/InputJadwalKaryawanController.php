<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalKaryawan;
use App\Models\User;
use App\Models\Shift;
use App\Models\Absensi;
use App\Models\Lembur;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Artisan;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class InputJadwalKaryawanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = JadwalKaryawan::with(['users', 'shift', 'absensi', 'lembur']);

        if ($user->role === 'spv') {
            $query->whereHas('users', function ($q) use ($user) {
                $q->where('divisi_id', $user->divisi_id);
            });
        }
        // Ambil nilai rentang tanggal dari request
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // Jika ada rentang tanggal, filter berdasarkan tanggal
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }
        // Ambil data yang sudah difilter
        $input_jadwals = $query->get();

        return view('input-jadwal_view.index', compact('input_jadwals', 'startDate', 'endDate'));
        // $input_jadwals = JadwalKaryawan::all();
        // return view('input-jadwal_view.index', compact('input_jadwals'));
    }

    public function create()
    {
        $users = User::all();
        $shifts = Shift::all();
        return view('input-jadwal_view.create', compact('users', 'shifts'));
    }

    public function store(Request $request)
    {
        // Cek apakah jadwal sudah ada untuk user_id, shift_id, dan tanggal
        $existingSchedule = JadwalKaryawan::where('user_id', $request->user_id)
            // ->where('shift_id', $request->shift_id)
            ->whereDate('tanggal', $request->tanggal)
            ->first();

        if ($existingSchedule) {
            // Jika jadwal sudah ada, kembalikan dengan pesan error
            return redirect()->back()->with('error', 'Jadwal sudah ada untuk tanggal tersebut.');
        }

        // try {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'shift_id' => 'required|exists:shift,id',
            'tanggal' => 'required|date',
            'cek_keterlambatan' => 'nullable',
            'lembur_jam' => 'nullable|numeric',
            'total_lembur' => 'nullable|numeric',
            'keterangan' => 'nullable',
            'minggu_ke' => 'nullable|numeric',
        ]);

        JadwalKaryawan::create([
            'user_id' => $request->user_id,
            'shift_id' => $request->shift_id,
            // 'absen_id' => $request->absen_id,
            // 'lembur_id' => $request->lembur_id,
            'tanggal' => $request->tanggal,
            // 'cek_keterlambatan' => $terlambat,
            // 'lembur_jam' => $request->lembur_jam ?? 0,
            // 'total_lembur' => $totalLembur, // Menyimpan total lembur
            // 'keterangan' => $request->keterangan,
            'minggu_ke' => Carbon::today()->startOfWeek(Carbon::SATURDAY)->weekOfYear,
        ]);
        // } catch (\Exception $e) {
        //     // Log error for debugging
        //     Log::error('Error updating input-jadwal: ' . $e->getMessage());
        //     return redirect()->route('input-jadwal.index')->with('error', 'Failed to update input-jadwal.');
        // }
        return redirect()->route('input-jadwal.index')->with('success', 'input-jadwal berhasil ditambahkan.');
    }

    public function import(Request $request)
    {
        // Validasi file CSV yang di-upload
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:10240', // Maksimal 10MB
        ]);

        // Ambil file CSV yang di-upload
        $file = $request->file('csv_file');
        $filePath = $file->getRealPath();

        // Baca file CSV menggunakan PhpSpreadsheet
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Ambil data dari sheet (dimulai dari baris kedua untuk mengabaikan header)
        $csvData = $sheet->toArray(null, true, true, true); // toArray mengubah ke array

        // Skip header jika ada
        $header = array_shift($csvData);

        // Validasi dan import data dari CSV
        foreach ($csvData as $row) {
            // Misalnya format CSV: nama_user, nama_shift, tanggal
            $userName = $row['A']; // Kolom A untuk nama_user
            $shiftName = $row['B']; // Kolom B untuk nama_shift
            $tanggal = $row['C']; // Kolom C untuk tanggal

            // Cek apakah user dan shift ada di database
            $user = User::where('nama_karyawan', $userName)->first();
            $shift = Shift::where('nama_shift', $shiftName)->first();

            // Debug: Cek apakah user dan shift ditemukan
            if (!$user) {
                dd("User tidak ditemukan: $userName");
            }

            if (!$shift) {
                dd("Shift tidak ditemukan: $shiftName");
            }

            // Validasi tanggal
            $validator = Validator::make(['tanggal' => $tanggal], [
                'tanggal' => 'required|date',
            ]);

            if ($validator->fails()) {
                dd("Tanggal tidak valid: $tanggal");
            }

            if ($user && $shift) {
                // Validasi tanggal
                $validator = Validator::make(['tanggal' => $tanggal], [
                    'tanggal' => 'required',
                ]);

                if ($validator->fails()) {
                    continue; // Skip jika ada tanggal yang tidak valid
                }

                // Cek apakah jadwal sudah ada untuk user_id, shift_id, dan tanggal
                $existingSchedule = JadwalKaryawan::where('user_id', $user->id)
                    // ->where('shift_id', $shift->id)
                    ->whereDate('tanggal', $tanggal)
                    ->first();

                // 1. JIKA JADWAL SUDAH ADA MAKA UPDATE
                if ($existingSchedule) {
                    // Jika jadwal sudah ada, update jadwal yang ada
                    $existingSchedule->update([
                        'shift_id' => $shift->id,
                        'tanggal' => Carbon::parse($tanggal),
                        'minggu_ke' => Carbon::parse($tanggal)->startOfWeek(Carbon::SATURDAY)->weekOfYear,
                    ]);
                } else {
                    // Jika jadwal belum ada, buat jadwal baru
                    JadwalKaryawan::create([
                        'user_id' => $user->id,
                        'shift_id' => $shift->id,
                        'tanggal' => Carbon::parse($tanggal),
                        'minggu_ke' => Carbon::parse($tanggal)->startOfWeek(Carbon::SATURDAY)->weekOfYear,
                    ]);
                }

                // // 2. JIKA JADWAL SUDAH ADA MAKA GAGAL
                // if ($existingSchedule) {
                //     // Jika jadwal sudah ada, lanjutkan dengan pesan kesalahan
                //     return redirect()->back()->with('error', "Jadwal sudah ada untuk $userName pada tanggal $tanggal.");
                // }
                // // Buat JadwalKaryawan
                // JadwalKaryawan::create([
                //     'user_id' => $user->id,
                //     'shift_id' => $shift->id,
                //     'tanggal' => Carbon::parse($tanggal),
                //     'minggu_ke' => Carbon::parse($tanggal)->startOfWeek(Carbon::SATURDAY)->weekOfYear,
                // ]);
            }
        }

        return redirect()->route('input-jadwal.index')->with('success', 'Jadwal berhasil diimpor.');
    }

    public function edit(JadwalKaryawan $input_jadwal)
    {
        $users = User::all();
        $shifts = Shift::all();
        $absensis = Absensi::all();
        $lemburs = Lembur::all();
        return view('input-jadwal_view.edit', compact('input_jadwal', 'users', 'shifts', 'absensis', 'lemburs'));
    }

    public function update(Request $request, JadwalKaryawan $input_jadwal)
    {
        try {
            $request->validate([
                'user_id' => 'required',
                'shift_id' => 'required',
                'absen_id' => 'nullable',
                'lembur_id' => 'nullable',
                'tanggal' => 'required|date',
                'cek_keterlambatan' => 'nullable',
                'lembur_jam' => 'nullable|numeric',
                'total_lembur' => 'nullable|numeric',
                'keterangan' => 'nullable',
                'minggu_ke' => 'nullable|numeric',
            ]);

            // Ambil data absen dan shift berdasarkan ID
            $absensi = Absensi::find($input_jadwal->absen_id);
            $shift = Shift::findOrFail($request->shift_id);

            if (!$absensi || $absensi->jam_masuk == null) {
                $terlambat = 2;
            } else {
                $shiftJamMasuk = \Carbon\Carbon::parse($shift->shift_masuk);
                $absenJamMasuk = \Carbon\Carbon::parse($absensi->jam_masuk);

                $terlambat = $absenJamMasuk->greaterThan($shiftJamMasuk);
            }



            // Hitung keterlambatan dalam menit
            // $keterlambatan = $terlambat ? $absenJamMasuk->diffInMinutes($shiftJamMasuk) : 0;
            if ($request->lembur_id == "No") {
                $input_jadwal->update([
                    // 'user_id' => $request->user_id,
                    'shift_id' => $request->shift_id,
                    // 'absen_id' => $request->absen_id,
                    'lembur_id' => null,
                    'tanggal' => $request->tanggal,
                    'cek_keterlambatan' => $terlambat,
                    'lembur_jam' => $request->lembur_jam ?? 0,
                    'total_lembur' => $totalLembur ?? 0,
                    'keterangan' => $request->keterangan,
                    // 'minggu_ke' => Carbon::today()->weekOfYear,
                ]);
            } else {
                // Ambil data lembur berdasarkan lembur_id
                $lembur = Lembur::findOrFail($request->lembur_id);

                // Menghitung total lembur: biaya_per_jam * lembur_jam
                $totalLembur = $lembur->biaya * ($request->lembur_jam ?? 0);

                $input_jadwal->update([
                    // 'user_id' => $request->user_id,
                    'shift_id' => $request->shift_id,
                    // 'absen_id' => $request->absen_id,
                    'lembur_id' => $request->lembur_id,
                    'tanggal' => $request->tanggal,
                    'cek_keterlambatan' => $terlambat,
                    'lembur_jam' => $request->lembur_jam ?? 0,
                    'total_lembur' => $totalLembur ?? 0,
                    'keterangan' => $request->keterangan,
                    // 'minggu_ke' => Carbon::today()->weekOfYear,
                ]);
            }

            // Redirect ke halaman input-jadwal dan beri pesan sukses
            return redirect()->route('input-jadwal.index')->with('success', 'input-jadwal berhasil diperbarui!');
        } catch (\Exception $e) {
            // Log error for debugging
            Log::error('Error updating input-jadwal: ' . $e->getMessage());
            return redirect()->route('input-jadwal.index')->with('error', 'Failed to update input-jadwal.');
        }
    }

    public function destroy(JadwalKaryawan $input_jadwal)
    {
        $input_jadwal->delete();
        return redirect()->route('input-jadwal.index')->with('success', 'input-jadwal deleted successfully.');
    }

    public function export(Request $request)
    {
        $user_id = $request->query('user_id');
        $minggu_ke = $request->query('minggu_ke');
        // Fetch the data from the database for a specific employee
        $employee = JadwalKaryawan::with(['users', 'shift', 'lembur', 'absensi'])
            ->where('user_id', $user_id) // Replace with actual employee ID or loop through employees
            ->where('minggu_ke', $minggu_ke)
            // ->whereBetween('tanggal', ['2025-03-26', '2025-04-04'])
            ->get();

        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Setting the period
        $sheet->setCellValue('B1', 'Periode');
        $sheet->setCellValue('C1', 'Minggu ke-' . $minggu_ke);

        // Employee name
        $sheet->setCellValue('B3', 'Nama');
        $sheet->setCellValue('C3', $employee->first()->users->nama_karyawan);
        // Shift (BJ or other)
        $sheet->setCellValue('C4', 'BJ'); // Replace with shift info (e.g., from the shift relationship)

        // Daily Attendance (Date and Time)
        $sheet->setCellValue('B6', 'Tanggal');
        $sheet->setCellValue('C6', 'Shift');
        $sheet->setCellValue('D6', 'Jam Masuk');
        // $sheet->setCellValue('E6', 'Keterangan');

        $row = 7;
        $mingguan = 0;
        $tottelat = 0;
        $kedatangan = 0;
        $totlembur = 0;
        $total = 0;
        foreach ($employee as $item) {
            $sheet->setCellValue('B' . $row, \Carbon\Carbon::parse($item->tanggal)->format('d-m-y'));
            $sheet->setCellValue('C' . $row, $item->shift->nama_shift); // Change as per the condition
            if ($item->shift->id == 9999) {
                $sheet->setCellValue('D' . $row, ''); // Change as per the condition
                // $sheet->setCellValue('E' . $row, ''); // Change as per the condition
            } else {
                $sheet->setCellValue('D' . $row, $item->absensi->jam_masuk); // Change as per the condition
                // $sheet->setCellValue('E' . $row, $item->keterlambatan_name); // Change as per the condition
            }
            if ($item->cek_keterlambatan == 0) {
                if ($item->shift->id != 9999) {
                    $mingguan = $mingguan + 15000;
                }
            } else {
                $tottelat++;
            }
            $totlembur = $totlembur + $item->total_lembur;
            $row++;
        }

        if ($tottelat > 0) {
            $kedatangan = 0;
        } else {
            $kedatangan = 40000;
        }

        // Weekly Summary
        $sheet->setCellValue('B15', 'Mingguan');
        $sheet->setCellValue('C15', $mingguan); // Replace with actual weekly total

        $sheet->setCellValue('B16', 'Kedatangan');
        $sheet->setCellValue('C16', $kedatangan); // Replace with actual attendance total

        $sheet->setCellValue('B17', 'Lembur');
        $sheet->setCellValue('C17', $totlembur); // Replace with actual overtime hours

        $sheet->setCellValue('B18', 'Total');
        $sheet->setCellValue('C18', $mingguan + $kedatangan + $totlembur); // Replace with the actual total

        $sheet->setCellValue('B19', 'Tanda Tangan');

        // Create a Writer instance
        $writer = new Xlsx($spreadsheet);

        // Set the response headers for downloading the Excel file
        $filename = $employee->first()->users->nama_karyawan . '_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment;filename="' . $filename . '"',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }

    public function export_all($minggu_ke)
    {
        $tahun = Carbon::now()->year;
        $startDate = Carbon::now()->setISODate($tahun, $minggu_ke + 1)->startOfWeek(Carbon::SATURDAY);
        $endDate = $startDate->copy()->addDays(6);

        $karyawanIds = JadwalKaryawan::where('minggu_ke', $minggu_ke)
            ->pluck('user_id')
            ->unique();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue("A1", 'Laporan Absensi Karyawan');
        $sheet->getStyle('A1')->getFont()
            ->setBold(true)
            ->setSize(18);
        $sheet->setCellValue("A3", 'Periode');
        $sheet->getStyle('A3')->getFont()
            ->setBold(true);
        $sheet->setCellValue("B3", Carbon::parse($startDate)->translatedFormat('d M Y') . ' - ' . Carbon::parse($endDate)->translatedFormat('d M Y'));

        $colOffset = 0;
        $rows = 5;
        $i = 0;

        foreach ($karyawanIds as $userId) {
            $row = $rows;

            $data = JadwalKaryawan::with(['users.toko', 'shift', 'lembur', 'absensi'])
                ->where('user_id', $userId)
                ->where('minggu_ke', $minggu_ke)
                ->get();

            if ($data->isEmpty()) continue;

            $user = $data->first()->users;

            $baseCol = chr(65 + $colOffset);        // Kolom pertama
            $nextCol = chr(65 + $colOffset + 1);    // Kolom kedua
            $nextCol2 = chr(65 + $colOffset + 2);   // Kolom ketiga

            // Header
            $sheet->setCellValue("{$baseCol}{$row}", 'Nama');
            $sheet->setCellValue("{$nextCol}{$row}", $user->nama_karyawan);
            $row++;
            $sheet->setCellValue("{$nextCol}{$row}", $user->toko->nama_toko ?? '-');
            $row++;

            $sheet->getColumnDimension($baseCol)->setWidth(13);    // Tanggal / Label
            $sheet->getColumnDimension($nextCol)->setWidth(15);    // Nama shift / nilai
            $sheet->getColumnDimension($nextCol2)->setWidth(11);   // Jam masuk

            $row++; // Spacer
            $headerRange = "{$baseCol}{$row}:{$nextCol2}{$row}";
            $sheet->setCellValue("{$baseCol}{$row}", 'Tanggal');
            $sheet->setCellValue("{$nextCol}{$row}", 'Shift');
            $sheet->setCellValue("{$nextCol2}{$row}", 'Jam Masuk');
            $sheet->getStyle($headerRange)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle($headerRange)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
            $row++;

            $mingguan = 0;
            $tottelat = 0;
            $kedatangan = 0;
            $totlembur = 0;

            foreach ($data as $item) {
                $sheet->setCellValue("{$baseCol}{$row}", Carbon::parse($item->tanggal)->format('d M Y'));
                $sheet->setCellValue("{$nextCol}{$row}", $item->shift->nama_shift ?? '-');
                $jamMasuk = ($item->shift->id != 9999 && $item->absensi) ? $item->absensi->jam_masuk : '-';
                $sheet->setCellValue("{$nextCol2}{$row}", $jamMasuk);


                if ($item->cek_keterlambatan == 0 && $item->shift->id != 9999) {
                    $mingguan += 15000;
                } else {
                    $tottelat++;
                }

                $totlembur += $item->total_lembur;
                $row++;
            }
            $sheet->getStyle("{$baseCol}{$row}:{$nextCol2}{$row}")->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);

            $kedatangan = ($tottelat > 0) ? 0 : 40000;
            $total = $mingguan + $kedatangan + $totlembur;

            $sheet->setCellValue("{$baseCol}" . ($row + 1), 'Mingguan');
            $sheet->setCellValue("{$nextCol}" . ($row + 1), $mingguan);
            $sheet->setCellValue("{$baseCol}" . ($row + 2), 'Kedatangan');
            $sheet->setCellValue("{$nextCol}" . ($row + 2), $kedatangan);
            $sheet->setCellValue("{$baseCol}" . ($row + 3), 'Lembur');
            $sheet->setCellValue("{$nextCol}" . ($row + 3), $totlembur);
            $sheet->setCellValue("{$baseCol}" . ($row + 4), 'Total');
            $sheet->setCellValue("{$nextCol}" . ($row + 4), $total);
            $sheet->getStyle("{$nextCol}" . ($row + 1) . ":{$nextCol}" . ($row + 4))
                ->getNumberFormat()
                ->setFormatCode('"Rp" #,##0');
            $sheet->setCellValue("{$baseCol}" . ($row + 5), 'Tanda Tangan');

            // Tambahkan border luar (outline) ke seluruh blok user
            $startCell = "{$baseCol}{$rows}";
            $endCol = chr(65 + $colOffset + 2); // 3 kolom
            $endCell = "{$endCol}" . ($row + 7);
            $sheet->getStyle("{$startCell}:{$endCell}")->applyFromArray([
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ]);

            // Geser posisi: 3 ke samping, lalu turun ke bawah setiap 3 kolom
            $i++;
            if ($i % 4 == 0) {
                $rows = $row + 10; // Turun baris baru
                $colOffset = 0;
            } else {
                $colOffset += 4; // Geser ke kanan
            }
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Rekap_Mingguan_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment;filename="' . $filename . '"',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }


    public function generate()
    {
        // Menjalankan command generate:jadwal
        Artisan::call('generate:jadwal');

        // Redirect kembali dengan pesan sukses
        return redirect()->back()->with('success', 'Jadwal karyawan berhasil digenerate.');
    }

    // public function generateBulanan()
    // {

    //     $bulan = Carbon::now()->month;
    //     $divisiId = auth()->user()->divisi_id;
    //     // Menjalankan command generate:jadwal
    //     Artisan::call('generate:jadwal-bulanan', [
    //         'bulan' => $bulan,
    //         'divisi_id' => $divisiId,
    //     ]);

    //     // Redirect kembali dengan pesan sukses
    //     return redirect()->back()->with('success', 'Jadwal karyawan berhasil digenerate.');
    // }

    public function generateBulanan(Request $request)
    {
        $bulan = Carbon::now()->month;
        $divisiId = auth()->user()->divisi_id;
        $overwrite = $request->input('overwrite', false); // default: false

        // Kirim ke artisan command
        Artisan::call('generate:jadwal-bulanan', [
            'bulan' => $bulan,
            'divisi_id' => $divisiId,
            '--overwrite' => $overwrite ? true : false,
        ]);

        return redirect()->back()->with('success', 'Jadwal karyawan berhasil digenerate. Mode: ' . ($overwrite ? 'Menimpa Jadwal Lama' : 'Tidak Menimpa'));
    }

    // public function export()
    // {
    //     // Fetch the data from the database
    //     $jadwalKaryawan = JadwalKaryawan::with(['users', 'shift', 'lembur', 'absensi'])
    //         ->get();

    //     // Create a new Spreadsheet object
    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();

    //     // Set the headers for the Excel sheet
    //     $sheet->setCellValue('A1', 'Nama Karyawan');
    //     $sheet->setCellValue('B1', 'Shift ID');
    //     $sheet->setCellValue('C1', 'Lembur ID');
    //     $sheet->setCellValue('D1', 'Absen ID');
    //     $sheet->setCellValue('E1', 'Tanggal');
    //     $sheet->setCellValue('F1', 'Cek Keterlambatan');
    //     $sheet->setCellValue('G1', 'Lembur Jam');
    //     $sheet->setCellValue('H1', 'Total Lembur');
    //     $sheet->setCellValue('I1', 'Keterangan');
    //     $sheet->setCellValue('J1', 'Minggu ke');

    //     // Fill the data into the Excel sheet
    //     $row = 2; // Start from the second row for the data
    //     foreach ($jadwalKaryawan as $item) {
    //         $sheet->setCellValue('A' . $row, $item->users->nama_karyawan); // Name of the employee
    //         $sheet->setCellValue('B' . $row, $item->shift->nama_shift); // Shift Name
    //         $sheet->setCellValue('C' . $row, $item->lembur ? $item->lembur->tipe_lembur : 'No Lembur');
    //         $sheet->setCellValue('D' . $row, $item->absensi->id ? $item->absensi->id : 'No Absen');
    //         $sheet->setCellValue('E' . $row, $item->tanggal);
    //         $sheet->setCellValue('F' . $row, $item->cek_keterlambatan ? 'Yes' : 'No');
    //         $sheet->setCellValue('G' . $row, $item->lembur_jam);
    //         $sheet->setCellValue('H' . $row, $item->total_lembur);
    //         $sheet->setCellValue('I' . $row, $item->keterangan);
    //         $sheet->setCellValue('J' . $row, $item->minggu_ke);
    //         $row++;
    //     }

    //     // Create a Writer instance
    //     $writer = new Xlsx($spreadsheet);

    //     // Set the response headers for downloading the Excel file
    //     $filename = 'jadwal_karyawan_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
    //     return response()->stream(
    //         function () use ($writer) {
    //             $writer->save('php://output');
    //         },
    //         200,
    //         [
    //             'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    //             'Content-Disposition' => 'attachment;filename="' . $filename . '"',
    //             'Cache-Control' => 'max-age=0',
    //         ]
    //     );
    // }
}
