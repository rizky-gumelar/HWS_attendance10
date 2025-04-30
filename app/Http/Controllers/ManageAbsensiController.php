<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Shift;
use App\Models\User;
use App\Models\JadwalKaryawan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ManageAbsensiController extends Controller
{
    public function index(Request $request)
    {
        $query = Absensi::with(['users']);
        // Ambil nilai rentang tanggal dari request
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // Jika ada rentang tanggal, filter berdasarkan tanggal
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }
        // Ambil data yang sudah difilter
        $absensis = $query->get();

        return view('absensi_view.index', compact('absensis', 'startDate', 'endDate'));

        // $absensis = Absensi::all();
        // return view('absensi_view.index', compact('absensis'));
    }

    public function import(Request $request)
    {
        $errors = [];

        // Validasi file
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls,txt|max:10240',
        ]);

        // Ambil file
        $file = $request->file('file');
        $filePath = $file->getRealPath();

        try {
            $spreadsheet = IOFactory::load($filePath);
        } catch (\Exception $e) {
            return back()->with('error', 'File tidak dapat dibaca. Pastikan formatnya benar.');
        }

        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true); // asumsikan baris pertama adalah header
        $header = array_shift($rows);

        $mingguKe = Carbon::now()->weekOfYear;
        JadwalKaryawan::where('minggu_ke', $mingguKe)->update(['absen_id' => null]);

        foreach ($rows as $row) {
            $userId = trim(str_replace("\xEF\xBB\xBF", '', $row['A'] ?? ''));
            $tanggalJamMasuk = $row['B'] ?? '';

            if (!$userId || !$tanggalJamMasuk) {
                continue;
            }

            try {
                $tanggal = Carbon::parse(explode(' ', $tanggalJamMasuk)[0])->format('Y-m-d');
                $jamMasuk = explode(' ', $tanggalJamMasuk)[1] ?? null;
            } catch (\Exception $e) {
                $errors[] = "Format tanggal tidak valid: " . $tanggalJamMasuk;
                continue;
            }

            $userExists = User::where('id', $userId)->exists();
            if (!$userExists) {
                $errors[] = "User ID tidak ditemukan: $userId";
                continue;
            }

            if (!$jamMasuk) {
                $errors[] = "Jam masuk kosong atau tidak valid untuk user ID: $userId";
                continue;
            }

            // Simpan atau update absensi
            Absensi::updateOrCreate(
                ['user_id' => $userId, 'tanggal' => $tanggal],
                ['jam_masuk' => $jamMasuk]
            );
        }

        $this->updateJadwalKaryawanWithAbsensi();

        if (!empty($errors)) {
            return redirect()->back()
                ->with('success', 'Data berhasil diimpor sebagian.')
                ->withErrors($errors);
        }

        return redirect()->back()->with('success', 'Data berhasil diimpor!');
    }

    private function updateJadwalKaryawanWithAbsensi()
    {
        // Ambil semua data absensi yang baru diimpor
        $absensi = Absensi::all();

        // Loop untuk setiap absensi
        foreach ($absensi as $item) {
            /// Ambil shift_id dari jadwal karyawan berdasarkan user_id dan tanggal
            $jadwalKaryawan = JadwalKaryawan::where('user_id', $item->user_id)
                ->where('tanggal', $item->tanggal)
                ->first();

            // Cek jika jadwal karyawan ditemukan
            if ($jadwalKaryawan && $jadwalKaryawan->shift_id) {
                // Ambil shift dari jadwal karyawan
                $shift = Shift::where('id', $jadwalKaryawan->shift_id)->first();

                // Cek jika shift ditemukan dan shift_masuk tidak kosong
                if ($shift && $shift->shift_masuk) {
                    // Bandingkan jam_masuk dengan shift_masuk
                    $shiftMasuk = Carbon::parse($shift->shift_masuk);
                    $jamMasuk = Carbon::parse($item->jam_masuk);

                    // Cek keterlambatan
                    $cekKeterlambatan = $jamMasuk->gt($shiftMasuk); // Perbandingan jika jam_masuk > shift_masuk
                    // $user = $jadwalKaryawan->users;
                    // if ($cekKeterlambatan) {
                    //     $user->poin_tidak_hadir = $user->poin_tidak_hadir - 0.5;
                    // }
                    // $user->save();

                    // Update jadwal karyawan
                    JadwalKaryawan::where('user_id', $item->user_id)
                        ->where('tanggal', $item->tanggal)
                        ->update([
                            'absen_id' => $item->id,
                            'cek_keterlambatan' => $cekKeterlambatan,  // Update keterlambatan
                        ]);
                }
            } else {
                $shift = Shift::where('id', $item->users->default_shift_id)->first();
                if ($shift && $shift->shift_masuk) {

                    $shiftMasuk = Carbon::parse($shift->shift_masuk);
                    $jamMasuk = Carbon::parse($item->jam_masuk);

                    // Cek keterlambatan
                    $cekKeterlambatan = $jamMasuk->gt($shiftMasuk); // Perbandingan jika jam_masuk > shift_masuk
                    // Jika jadwal belum ada, buat jadwal baru
                    JadwalKaryawan::create([
                        'user_id' => $item->user_id,
                        'shift_id' => $item->users->default_shift_id,
                        'absen_id' => $item->id,
                        'cek_keterlambatan' => $cekKeterlambatan,
                        'tanggal' => Carbon::parse($item->tanggal),
                        'minggu_ke' => Carbon::parse($item->tanggal)->startOfWeek(Carbon::SATURDAY)->weekOfYear,
                    ]);
                }
            }
        }
    }

    // public function create()
    // {
    //     return view('absensi_view.create');
    // }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'nama_shift'  => 'required|string|max:255',
    //         'absensi_masuk' => 'required',
    //         'absensi_keluar' => 'required|after:absensi_masuk',
    //     ]);

    //     Absensi::create([
    //         'nama_shift' => $request->nama_shift,
    //         'absensi_masuk' => $request->absensi_masuk,
    //         'absensi_keluar' => $request->absensi_keluar,
    //     ]);

    //     return redirect()->route('shift.index')->with('success', 'Shift berhasil ditambahkan.');
    // }

    // public function edit(Shift $shift)
    // {
    //     return view('absensi_view.edit', compact('shift'));
    // }

    // public function update(Request $request, Shift $shift)
    // {
    //     try {
    //         $request->validate([
    //             'nama_shift' => 'required|string|max:255',
    //             'absensi_masuk' => 'required',
    //             'absensi_keluar' => 'required|after:absensi_masuk',
    //         ]);

    //         $shift->update([
    //             'nama_shift' => $request->nama_shift,
    //             'absensi_masuk' => $request->absensi_masuk,
    //             'absensi_keluar' => $request->absensi_keluar,
    //         ]);

    //         // Redirect ke halaman shift dan beri pesan sukses
    //         return redirect()->route('shift.index')->with('success', 'Shift berhasil diperbarui!');
    //     } catch (\Exception $e) {
    //         // Log error for debugging
    //         Log::error('Error updating shift: ' . $e->getMessage());
    //         return redirect()->route('shift.index')->with('error', 'Failed to update shift.');
    //     }
    // }

    public function destroy(Absensi $absensi)
    {
        $absensi->delete();
        return redirect()->route('absensi.index')->with('success', 'Shift deleted successfully.');
    }
}
