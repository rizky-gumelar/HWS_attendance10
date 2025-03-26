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

class InputJadwalKaryawanController extends Controller
{
    public function index()
    {
        $input_jadwals = JadwalKaryawan::all();
        return view('input-jadwal_view.index', compact('input_jadwals'));
    }

    public function create()
    {
        $users = User::all();
        $shifts = Shift::all();
        return view('input-jadwal_view.create', compact('users', 'shifts'));
    }

    public function store(Request $request)
    {
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

        // // Ambil data absen dan shift berdasarkan ID
        // $shift = Shift::findOrFail($request->shift_id);
        // $absensi = Absensi::find($request->absen_id);

        // $terlambat = false;

        // if ($absensi) {
        //     // Jika data absensi ditemukan, periksa jam masuknya
        //     $shiftJamMasuk = Carbon::parse($shift->shift_masuk);
        //     $absenJamMasuk = Carbon::parse($absensi->jam_masuk);

        //     // Cek apakah karyawan terlambat
        //     $terlambat = $absenJamMasuk->greaterThan($shiftJamMasuk);
        // }

        // // Ambil data lembur berdasarkan lembur_id
        // $lembur = Lembur::findOrFail($request->lembur_id);

        // // Menghitung total lembur: biaya_per_jam * lembur_jam
        // $totalLembur = $lembur->biaya_per_jam * ($request->lembur_jam ?? 0);

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
            'minggu_ke' => Carbon::today()->weekOfYear,
        ]);
        // } catch (\Exception $e) {
        //     // Log error for debugging
        //     Log::error('Error updating input-jadwal: ' . $e->getMessage());
        //     return redirect()->route('input-jadwal.index')->with('error', 'Failed to update input-jadwal.');
        // }
        return redirect()->route('input-jadwal.index')->with('success', 'input-jadwal berhasil ditambahkan.');
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

            // Membandingkan jam masuk shift dengan jam masuk absen
            $shiftJamMasuk = \Carbon\Carbon::parse($shift->shift_masuk);
            $absenJamMasuk = \Carbon\Carbon::parse($absensi->jam_masuk);

            // Cek apakah karyawan terlambat
            $terlambat = $absenJamMasuk->greaterThan($shiftJamMasuk);

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

    public function export($user_id)
    {
        // Fetch the data from the database for a specific employee
        $employee = JadwalKaryawan::with(['users', 'shift', 'lembur', 'absensi'])
            ->where('user_id', $user_id) // Replace with actual employee ID or loop through employees
            ->whereBetween('tanggal', ['2025-03-26', '2025-04-04'])
            ->get();

        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Setting the period
        $sheet->setCellValue('B1', 'Periode');
        $sheet->setCellValue('C1', '15-21 Maret 2025');

        // Employee name
        $sheet->setCellValue('B3', 'Nama');
        $sheet->setCellValue('C3', $employee->first()->users->nama_karyawan); // Replace with actual employee name

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
            if ($item->shift->id == 99) {
                $sheet->setCellValue('D' . $row, ''); // Change as per the condition
                // $sheet->setCellValue('E' . $row, ''); // Change as per the condition
            } else {
                $sheet->setCellValue('D' . $row, $item->absensi->jam_masuk); // Change as per the condition
                // $sheet->setCellValue('E' . $row, $item->keterlambatan_name); // Change as per the condition
            }
            if ($item->cek_keterlambatan == 0) {
                if ($item->shift->id != 99) {
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
        $filename = 'jadwal_karyawan_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
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
