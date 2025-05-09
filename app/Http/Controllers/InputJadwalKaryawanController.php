<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalKaryawan;
use App\Models\User;
use App\Models\Shift;
use App\Models\Absensi;
use App\Models\LaporanMingguan;
use App\Models\Lembur;
use App\Models\Libur;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Artisan;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
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
                $q->where('divisi_id', $user->divisi_id)->where('role', '!=', 'admin');
            });
        }

        // Ambil nilai rentang tanggal dari request
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // Jika tidak ada tanggal, gunakan tanggal bulan ini sebagai default
        if (!$startDate || !$endDate) {
            $startDate = Carbon::now()->startOfMonth()->toDateString();
            $endDate = Carbon::now()->endOfMonth()->toDateString();
        }

        // Filter berdasarkan tanggal
        $query->whereBetween('tanggal', [$startDate, $endDate]);

        // Ambil data yang sudah difilter
        $input_jadwals = $query->get();

        return view('input-jadwal_view.index', compact('input_jadwals', 'startDate', 'endDate'));
    }

    public function read(Request $request)
    {
        $user = auth()->user();

        $query = JadwalKaryawan::with(['users', 'shift', 'absensi', 'lembur']);

        if ($user->role === 'spv') {
            $query->whereHas('users', function ($q) use ($user) {
                $q->where('divisi_id', $user->divisi_id)->where('role', '!=', 'admin');
            });
        } else if ($user->role === 'karyawan') {
            $query->whereHas('users', function ($q) use ($user) {
                $q->where('id', $user->id)->where('role', '!=', 'admin');
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

        return view('input-jadwal_view.read', compact('input_jadwals', 'startDate', 'endDate'));
        // $input_jadwals = JadwalKaryawan::all();
        // return view('input-jadwal_view.index', compact('input_jadwals'));
    }

    public function create()
    {
        $user = auth()->user();

        $query = User::orderBy('nama_karyawan');
        if ($user->role === 'spv') {
            $query->where('divisi_id', $user->divisi_id)->where('role', '!=', 'admin');
        }
        $users = $query->get();
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
        $errors = [];
        $toleransi = Setting::where('key', 'toleransi_masuk')->value('value');
        // Validasi file CSV yang di-upload
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt,xlsx|max:10240', // Maksimal 10MB
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

            if (empty(trim($row['A'])) && empty(trim($row['B'])) && empty(trim($row['C']))) {
                continue;
            }


            // Debug: Cek apakah user dan shift ditemukan
            if (!$user) {
                $errors[] = "User tidak ditemukan: $userName";
                continue;
            }

            if (!$shift) {
                $errors[] = "Shift tidak ditemukan: $shiftName";
                continue;
            }

            // Validasi tanggal
            $validator = Validator::make(['tanggal' => $tanggal], [
                'tanggal' => 'required|date',
            ]);

            if ($validator->fails()) {
                $errors[] = "Tanggal tidak valid: $tanggal";
                continue;
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
                    // Ambil data absen dan shift berdasarkan ID
                    $absensi = Absensi::find($existingSchedule->absen_id);
                    if (!$absensi || $absensi->jam_masuk == null) {
                        $terlambat = 2;
                    } else {
                        $shiftJamMasuk = \Carbon\Carbon::parse($shift->shift_masuk);
                        $absenJamMasuk = \Carbon\Carbon::parse($absensi->jam_masuk);

                        $shiftMasukWithTolerance = $shiftJamMasuk->copy()->addMinutes($toleransi);
                        $terlambat = $absenJamMasuk->greaterThan($shiftMasukWithTolerance) ? 1 : 0;
                    }
                    // Jika jadwal sudah ada, update jadwal yang ada
                    $existingSchedule->update([
                        'shift_id' => $shift->id,
                        'tanggal' => Carbon::parse($tanggal),
                        'cek_keterlambatan' => $terlambat,
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

        if (!empty($errors)) {
            return redirect()->route('lembur.import')
                ->with('success', 'Jadwal berhasil diimpor sebagian.')
                ->withErrors($errors);
        }

        return redirect()->route('input-jadwal.index')->with('success', 'Jadwal berhasil diimpor.');
    }

    public function importlembur(Request $request)
    {
        $errors = [];
        // Validasi file CSV yang di-upload
        $request->validate([
            'csv_file' => 'required|mimes:csv,xlsx|max:10240', // Maksimal 10MB
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
            $tanggal = Carbon::parse($row['A'])->format('Y-m-d'); // Kolom C untuk tanggal
            $userName = $row['B']; // Kolom A untuk nama_user
            $lemburName = $row['C']; // Kolom B untuk nama_shift
            $durasi = $row['E'];
            $keterangan = $row['G']; // Kolom B untuk nama_shift

            // Cek apakah user dan shift ada di database
            $user = User::where('nama_karyawan', $userName)->first();
            $lembur = Lembur::where('tipe_lembur', $lemburName)->first();

            if (empty(trim($row['A'])) && empty(trim($row['B'])) && empty(trim($row['C'])) && empty(trim($row['D'])) && empty(trim($row['E']))) {
                continue;
            }

            try {
                $tanggal = Carbon::parse($row['A'])->format('Y-m-d');
            } catch (\Exception $e) {
                $errors[] = "Format tanggal tidak valid: " . $row['A'];
                continue;
            }

            // Debug: Cek apakah user dan shift ditemukan
            if (!$user) {
                $errors[] = "User tidak ditemukan: $userName";
                continue;
            }

            if (!$lembur) {
                $errors[] = "Tipe lembur tidak ditemukan: $lemburName";
                continue;
            }

            if (!$keterangan) {
                $keterangan = "-";
            }

            // Validasi tanggal
            $validator = Validator::make(['tanggal' => $tanggal], [
                'tanggal' => 'required|date',
            ]);

            if ($validator->fails()) {
                $errors[] = "Tanggal tidak valid: $tanggal";
                continue;
            }

            if (!is_numeric($durasi) || $durasi < 0) {
                continue; // Skip jika durasi tidak valid
            }


            // Cek apakah jadwal sudah ada untuk user_id, lembur_id, dan tanggal
            $existingSchedule = JadwalKaryawan::where('user_id', $user->id)
                // ->where('shift_id', $shift->id)
                ->whereDate('tanggal', $tanggal)
                ->first();

            // 1. JIKA JADWAL SUDAH ADA MAKA UPDATE
            if ($existingSchedule) {
                // Jika jadwal sudah ada, update jadwal yang ada
                $existingSchedule->update([
                    'lembur_id' => $lembur->id,
                    'tanggal' => Carbon::parse($tanggal),
                    'lembur_jam' => $durasi,
                    'total_lembur' => $durasi * $lembur->biaya,
                    'keterangan' => $keterangan,
                ]);
            } else {
                // dd([
                //     'tanggal' => $tanggal,
                //     'existing' => JadwalKaryawan::where('user_id', $user->id)
                //         ->whereDate('tanggal', $tanggal)
                //         ->get()
                // ]);
                // Jika jadwal belum ada, buat jadwal baru
                JadwalKaryawan::create([
                    'user_id' => $user->id,
                    'shift_id' => $user->default_shift_id,
                    'lembur_id' => $lembur->id,
                    'lembur_jam' => $durasi,
                    'total_lembur' => $durasi * $lembur->biaya,
                    'keterangan' => $keterangan,
                    'tanggal' => Carbon::parse($tanggal),
                    'minggu_ke' => Carbon::parse($tanggal)->startOfWeek(Carbon::SATURDAY)->weekOfYear,
                ]);
            }
        }

        if (!empty($errors)) {
            return redirect()->route('lembur.import')
                ->with('success', 'Lembur berhasil diimpor sebagian.')
                ->withErrors($errors);
        }

        return redirect()->route('lembur.import')->with('success', 'Lembur berhasil diimpor.');
    }

    public function deleteLembur(JadwalKaryawan $input_jadwal)
    {
        $input_jadwal->update([
            'lembur_id' => null,
            'lembur_jam' => 0,
            'total_lembur' => 0,
            'keterangan' => null,
        ]);
        return redirect()->route('lembur.import')->with('success', 'Jadwal berhasil dihapus.');
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
        $toleransi = Setting::where('key', 'toleransi_masuk')->value('value');
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

                $shiftMasukWithTolerance = $shiftJamMasuk->copy()->addMinutes($toleransi);
                $terlambat = $absenJamMasuk->greaterThan($shiftMasukWithTolerance) ? 1 : 0;
            }

            // Hitung keterlambatan dalam menit
            // $keterlambatan = $terlambat ? $absenJamMasuk->diffInMinutes($shiftJamMasuk) : 0;
            if ($request->lembur_id == "No") {
                $input_jadwal->update([
                    'user_id' => $request->user_id,
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
                $lembur = Lembur::find($request->lembur_id);

                // Menghitung total lembur: biaya_per_jam * lembur_jam
                $totalLembur = $lembur->biaya * ($request->lembur_jam ?? 0);

                $input_jadwal->update([
                    'user_id' => $request->user_id,
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
        return redirect()->back()->with('success', 'input-jadwal deleted successfully.');
    }


    public function export_all($minggu_ke)
    {
        $tahun = Carbon::now()->year;
        $startDate = Carbon::now()->setISODate($tahun, $minggu_ke + 1)->startOfWeek(Carbon::SATURDAY);
        $endDate = $startDate->copy()->addDays(6);

        $karyawanIds = JadwalKaryawan::where('minggu_ke', $minggu_ke)
            ->join('users', 'jadwal_karyawan.user_id', '=', 'users.id')
            ->where('users.role', '!=', 'admin')
            ->where('users.status', '=', 'aktif')
            ->orderBy('users.toko_id')
            ->orderBy('users.nama_karyawan')
            ->pluck('jadwal_karyawan.user_id')
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
        $blockIndex = 0;
        $initialRow = 5;
        $lastRowUsed = $rows;
        foreach ($karyawanIds as $userId) {
            $row = $rows;

            $data = JadwalKaryawan::with(['users.toko', 'shift', 'lembur', 'absensi'])
                ->where('user_id', $userId)
                ->where('minggu_ke', $minggu_ke)
                ->get();

            $laporanMingguan = LaporanMingguan::where('minggu_ke', $minggu_ke)
                ->where('user_id', $userId)->first();
            // Memeriksa status hanya jika ada entri yang ditemukan
            if ($laporanMingguan) {
                $color = ($laporanMingguan->status == 'kurang') ? 'FFFF0000' : '00000000';
            } else {
                // Jika tidak ditemukan data
                $color = 'FFFF0000'; // Default color jika tidak ada data
            }

            if ($data->isEmpty()) continue;

            $user = $data->first()->users;

            $baseCol = chr(65 + $colOffset);        // Kolom pertama
            $nextCol = chr(65 + $colOffset + 1);    // Kolom kedua
            $nextCol2 = chr(65 + $colOffset + 2);   // Kolom ketiga
            $nextCol3 = chr(65 + $colOffset + 3);   // Kolom ketiga

            // Header
            $sheet->setCellValue("{$baseCol}{$row}", 'Nama');
            $sheet->setCellValue("{$nextCol}{$row}", $user->nama_karyawan);
            $sheet->setCellValue("{$nextCol3}{$row}", ' ');
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

            $mingguan = $laporanMingguan->uang_mingguan;
            $tottelat = 0;
            $kedatangan = $laporanMingguan->uang_kedatangan;
            $totlembur = $laporanMingguan->uang_lembur_mingguan;

            foreach ($data as $item) {
                $sheet->setCellValue("{$baseCol}{$row}", Carbon::parse($item->tanggal)->format('d M Y'));
                $isLibur = Libur::isLibur($item->tanggal);
                $keteranganLibur = $isLibur ? Libur::getLibur($item->tanggal)->keterangan : null;
                $sheet->setCellValue("{$nextCol}{$row}", $keteranganLibur ?? $item->shift->nama_shift ?? '-');
                $jamMasuk = ($item->shift->id != 9999 && $item->absensi) ? $item->absensi->jam_masuk : '-';
                $sheet->setCellValue("{$nextCol2}{$row}", $jamMasuk);

                // if (($item->cek_keterlambatan == 0 && $item->shift->id != 9999) || $isLibur) {
                //     $mingguan += 15000;
                // } else {
                //     $tottelat++;
                // }

                // $totlembur += $item->total_lembur;
                $row++;
            }
            $sheet->getStyle("{$baseCol}{$row}:{$nextCol2}{$row}")->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);

            // $kedatangan = ($tottelat > 0) ? 0 : 40000;
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
                        'color' => ['argb' => $color],
                    ],
                ],
            ]);
            $lastRowUsed = $row + 10;
            // Geser posisi: 3 ke samping, lalu turun ke bawah setiap 3 kolom
            $i++;

            // Cek apakah sudah isi 4 kolom dan 4 baris (16 user)
            if ($i % 16 == 0) {
                // Tambahkan page break
                $sheet->setBreak("A{$lastRowUsed}", Worksheet::BREAK_ROW);

                $blockIndex++;
                $rows = $lastRowUsed + 2; // baris baru setelah blok sebelumnya
                $colOffset = 0;

                // Tambahkan kembali header
                $sheet->setCellValue("A{$rows}", 'Laporan Absensi Karyawan');
                $sheet->getStyle("A{$rows}")->getFont()->setBold(true)->setSize(18);

                $sheet->setCellValue("A" . ($rows + 2), 'Periode');
                $sheet->getStyle("A" . ($rows + 2))->getFont()->setBold(true);
                $sheet->setCellValue("B" . ($rows + 2), Carbon::parse($startDate)->translatedFormat('d M Y') . ' - ' . Carbon::parse($endDate)->translatedFormat('d M Y'));

                $rows += 4; // mulai posisi isi di bawah header
            } elseif ($i % 4 == 0) {
                $rows = $lastRowUsed; // turun ke bawah blok sebelumnya
                $colOffset = 0;
            } else {
                $colOffset += 4; // geser ke kanan
            }
        }

        // Buat Sheet baru untuk Detail Lembur
        $sheetLembur = $spreadsheet->createSheet();
        $sheetLembur->setTitle('Detail Lembur');

        $sheetLembur->setCellValue("A1", 'Detail Lembur Mingguan');
        $sheetLembur->getStyle('A1')->getFont()
            ->setBold(true)
            ->setSize(18);
        // Header kolom
        $sheetLembur->setCellValue('A3', 'Tanggal');
        $sheetLembur->setCellValue('B3', 'Nama');
        $sheetLembur->setCellValue('C3', 'Tipe Lembur');
        $sheetLembur->setCellValue('D3', 'Durasi');
        $sheetLembur->setCellValue('E3', 'Total');
        $sheetLembur->setCellValue('F3', 'Keterangan');
        $sheetLembur->getColumnDimension('A')->setWidth(13);
        $sheetLembur->getColumnDimension('B')->setWidth(20);
        $sheetLembur->getColumnDimension('C')->setWidth(15);
        $sheetLembur->getColumnDimension('D')->setWidth(13);
        $sheetLembur->getColumnDimension('E')->setWidth(13);
        $sheetLembur->getColumnDimension('F')->setWidth(25);

        // Ambil data lembur
        $lemburData = JadwalKaryawan::with(['users', 'lembur'])
            ->where('minggu_ke', $minggu_ke)
            ->whereNotNull('lembur_id') // hanya yang ada lembur
            ->orderBy('tanggal', 'asc')
            ->orderBy('lembur_id')
            ->get();

        $row = 4;
        $grandTotal = 0;

        foreach ($lemburData as $item) {
            $sheetLembur->setCellValue("A{$row}", Carbon::parse($item->tanggal)->format('d M Y'));
            $sheetLembur->setCellValue("B{$row}", $item->users->nama_karyawan ?? '-');
            $sheetLembur->setCellValue("C{$row}", $item->lembur->tipe_lembur ?? '-');
            $sheetLembur->setCellValue("D{$row}", $item->lembur_jam ?? 0);
            $sheetLembur->setCellValue("E{$row}", $item->total_lembur ?? 0);
            $sheetLembur->setCellValue("F{$row}", $item->keterangan ?? '-');

            $grandTotal += $item->total_lembur ?? 0;
            $row++;
        }

        // Tambahkan baris total
        $sheetLembur->setCellValue("D{$row}", 'Grand Total');
        $sheetLembur->setCellValue("E{$row}", $grandTotal);

        // Format angka pada kolom E
        $sheetLembur->getStyle("E2:E{$row}")
            ->getNumberFormat()
            ->setFormatCode('"Rp" #,##0');

        // Tambahkan border untuk semua data
        $sheetLembur->getStyle("A3:F{$row}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

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

    public function export(Request $request)
    {
        $user_id = $request->query('user_id');
        $minggu_ke = $request->query('minggu_ke');

        $tahun = Carbon::now()->year;
        $startDate = Carbon::now()->setISODate($tahun, $minggu_ke + 1)->startOfWeek(Carbon::SATURDAY);
        $endDate = $startDate->copy()->addDays(6);

        $karyawanIds = JadwalKaryawan::where('minggu_ke', $minggu_ke)
            ->join('users', 'jadwal_karyawan.user_id', '=', 'users.id')
            ->where('users.role', '!=', 'admin')
            ->where('user_id', $user_id)
            ->pluck('jadwal_karyawan.user_id')
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

            $laporanMingguan = LaporanMingguan::where('minggu_ke', $minggu_ke)
                ->where('user_id', $userId)->first();
            // Memeriksa status hanya jika ada entri yang ditemukan
            if ($laporanMingguan) {
                $color = ($laporanMingguan->status == 'kurang') ? 'FFFF0000' : '00000000';
            } else {
                // Jika tidak ditemukan data
                $color = 'FFFF0000'; // Default color jika tidak ada data
            }

            if ($data->isEmpty()) continue;

            $user = $data->first()->users;

            $baseCol = chr(65 + $colOffset);        // Kolom pertama
            $nextCol = chr(65 + $colOffset + 1);    // Kolom kedua
            $nextCol2 = chr(65 + $colOffset + 2);   // Kolom ketiga
            $nextCol3 = chr(65 + $colOffset + 3);   // Kolom ketiga

            // Header
            $sheet->setCellValue("{$baseCol}{$row}", 'Nama');
            $sheet->setCellValue("{$nextCol}{$row}", $user->nama_karyawan);
            $sheet->setCellValue("{$nextCol3}{$row}", ' ');
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

            $mingguan = $laporanMingguan->uang_mingguan;
            $tottelat = 0;
            $kedatangan = $laporanMingguan->uang_kedatangan;
            $totlembur = $laporanMingguan->uang_lembur_mingguan;

            foreach ($data as $item) {
                $sheet->setCellValue("{$baseCol}{$row}", Carbon::parse($item->tanggal)->format('d M Y'));
                $isLibur = Libur::isLibur($item->tanggal);
                $keteranganLibur = $isLibur ? Libur::getLibur($item->tanggal)->keterangan : null;
                $sheet->setCellValue("{$nextCol}{$row}", $keteranganLibur ?? $item->shift->nama_shift ?? '-');
                $jamMasuk = ($item->shift->id != 9999 && $item->absensi) ? $item->absensi->jam_masuk : '-';
                $sheet->setCellValue("{$nextCol2}{$row}", $jamMasuk);

                // if (($item->cek_keterlambatan == 0 && $item->shift->id != 9999) || $isLibur) {
                //     $mingguan += 15000;
                // } else {
                //     $tottelat++;
                // }

                // $totlembur += $item->total_lembur;
                $row++;
            }
            $sheet->getStyle("{$baseCol}{$row}:{$nextCol2}{$row}")->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);

            // $kedatangan = ($tottelat > 0) ? 0 : 40000;
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
                        'color' => ['argb' => $color],
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

    public function generateBulananAdmin(Request $request)
    {
        $bulan = Carbon::now()->month;
        $overwrite = $request->input('overwrite', false); // default: false

        // Kirim ke artisan command
        Artisan::call('generate:jadwal-admin', [
            'bulan' => $bulan,
            '--overwrite' => $overwrite ? true : false,
        ]);

        return redirect()->back()->with('success', 'Jadwal karyawan berhasil digenerate. Mode: ' . ($overwrite ? 'Menimpa Jadwal Lama' : 'Tidak Menimpa'));
    }

    public function calendar(Request $request)
    {
        $divisiId = $request->input('divisi_id');
        $user = auth()->user();

        $query = JadwalKaryawan::with(['users', 'shift'])
            ->where('shift_id', '<', 100);

        if ($user->role === 'admin') {
            if ($divisiId) {
                $query->whereHas('users', function ($q) use ($divisiId) {
                    $q->where('divisi_id', $divisiId);
                });
            }
        } else if ($user->role === 'spv') {
            $query->whereHas('users', function ($q) use ($user) {
                $q->where('divisi_id', $user->divisi_id)->where('role', '!=', 'admin');
            });
        }

        // Ambil data yang sudah difilter
        $jadwal = $query->get();

        $jadwalEvents = $jadwal->map(function ($item) {
            $divisiColors = [
                '1' => '#b3d4fc', // Soft biru
                '2' => '#c9b3f7', // Soft ungu
                '3' => '#ffc89a', // Soft oranye
                '4' => '#9fe7d3', // Soft teal
                '5' => '#f7a7c4', // Soft pink
                '6' => '#a9e5bc', // Soft hijau
                '7' => '#ffe9a6', // Soft kuning
                '8' => '#9ed9e7', // Soft biru muda
                '9' => '#d1d3d4'  // Soft abu-abu
            ];

            $startDateTime = $item->tanggal . ' ' . $item->shift->shift_masuk;
            $endDateTime = $item->tanggal . ' ' . $item->shift->shift_keluar;

            // Handle shift malam (keluar di hari berikutnya)
            if (strtotime($endDateTime) < strtotime($startDateTime)) {
                $endDateTime = date('Y-m-d H:i:s', strtotime($endDateTime . ' +1 day'));
            }

            $divisi = $item->users->divisi_id;
            $color = $divisiColors[$divisi]; // Default warna jika divisi tidak cocok


            return [
                'title' => '(' . substr($item->shift->shift_masuk, 0, 2) . ' - ' .
                    substr($item->shift->shift_keluar, 0, 2) . ') ' . $item->users->nama_karyawan,
                'start' => $startDateTime,
                'end' => $endDateTime,
                'backgroundColor' => $color,
                'textColor' => '#fff'
            ];
        });

        // Ambil data libur
        $libur = Libur::all();

        $liburEvents = $libur->map(function ($item) {
            return [
                'title' => $item->keterangan,
                'start' => $item->tanggal,
                'end' => $item->tanggal,
                'allDay' => true,
                'backgroundColor' => '#ff8181',
                'textColor' => '#fff'
            ];
        });

        // Gabungkan semua event
        $events = $jadwalEvents->merge($liburEvents);

        return response()->json($events);
    }

    public function calendarLibur(Request $request)
    {
        $divisiId = $request->input('divisi_id');
        $user = auth()->user();

        $query = JadwalKaryawan::with(['users', 'shift'])
            ->where('shift_id', '>', 100);

        if ($user->role === 'admin') {
            if ($divisiId) {
                $query->whereHas('users', function ($q) use ($divisiId) {
                    $q->where('divisi_id', $divisiId);
                });
            }
        } else if ($user->role === 'spv') {
            $query->whereHas('users', function ($q) use ($user) {
                $q->where('divisi_id', $user->divisi_id)->where('role', '!=', 'admin');
            });
        }

        $jadwal = $query->get();

        $divisiColors = [
            '1' => '#b3d4fc',
            '2' => '#c9b3f7',
            '3' => '#ffc89a',
            '4' => '#9fe7d3',
            '5' => '#f7a7c4',
            '6' => '#a9e5bc',
            '7' => '#ffe9a6',
            '8' => '#9ed9e7',
            '9' => '#d1d3d4'
        ];

        $jadwalEvents = $jadwal->map(function ($item) use ($divisiColors) {
            $startDateTime = $item->tanggal . ' ' . $item->shift->shift_masuk;
            $endDateTime = $item->tanggal . ' ' . $item->shift->shift_keluar;

            if (strtotime($endDateTime) < strtotime($startDateTime)) {
                $endDateTime = date('Y-m-d H:i:s', strtotime($endDateTime . ' +1 day'));
            }

            $divisi = $item->users->divisi_id;
            $color = $divisiColors[$divisi] ?? '#cccccc';

            return [
                'title' => '(' . substr($item->shift->shift_masuk, 0, 2) . ' - ' .
                    substr($item->shift->shift_keluar, 0, 2) . ') ' . $item->users->nama_karyawan,
                'start' => $startDateTime,
                'end' => $endDateTime,
                'backgroundColor' => $color,
                'textColor' => '#fff'
            ];
        });

        // Ambil data libur
        $libur = Libur::all();

        $liburEvents = $libur->map(function ($item) {
            return [
                'title' => $item->keterangan,
                'start' => $item->tanggal,
                'end' => $item->tanggal,
                'allDay' => true,
                'backgroundColor' => '#ff8181',
                'textColor' => '#fff'
            ];
        });

        // Gabungkan semua event
        $events = $jadwalEvents->merge($liburEvents);

        return response()->json($events);
    }
}
