<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Shift;
use App\Models\User;
use App\Models\JadwalKaryawan;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
        // Validasi file yang diunggah
        $request->validate([
            'file' => 'required|mimes:dat,csv,txt,xlsx',
        ]);

        // Mendapatkan minggu yang akan diimpor, misalnya menggunakan tanggal sekarang
        $mingguKe = Carbon::now()->weekOfYear;

        // Mengosongkan absen_id untuk minggu yang sama
        JadwalKaryawan::where('minggu_ke', $mingguKe)->update(['absen_id' => null]);

        // Mendapatkan file yang diunggah
        $file = $request->file('file');
        $handle = fopen($file, 'r'); // Membuka file untuk dibaca

        if ($handle === false) {
            return back()->with('error', 'File tidak dapat dibuka.');
        }

        // Skip header baris pertama jika ada
        // fgetcsv($handle);

        // Membaca file baris per baris
        while (($row = fgetcsv($handle)) !== false) {
            // $userId = $row[0];
            $userId = trim(str_replace("\xEF\xBB\xBF", '', $row[0]));
            // Asumsi kolom: user_id, tanggal_jam_masuk (gabungan)
            // Misalnya data di file: 2025-03-01 08:00:00
            $tanggalJamMasuk = $row[1]; // Mengambil nilai kolom tanggal_jam_masuk

            // Pisahkan tanggal dan jam jika menggunakan spasi sebagai pemisah
            $tanggal = Carbon::parse(explode(' ', $tanggalJamMasuk)[0])->format('Y-m-d');
            $jamMasuk = explode(' ', $tanggalJamMasuk)[1];

            // Cek apakah user_id ada di tabel users
            $userExists = User::where('id', $userId)->exists();

            if (!$userExists) {
                // Jika user_id tidak ada, lanjutkan ke baris berikutnya
                continue;
            }

            // Cek apakah absensi ada untuk tanggal dan user tertentu
            $absensi = Absensi::where('user_id', $userId)
                ->where('tanggal', $tanggal)
                ->first();

            if (!$absensi) {
                // Simpan ke dalam database jika belum ada
                Absensi::create([
                    'user_id' => $row[0],
                    'tanggal' => $tanggal,
                    'jam_masuk' => $jamMasuk,
                ]);
            } else {
                $absensi->update([
                    'user_id' => $row[0],
                    'tanggal' => $tanggal,
                    'jam_masuk' => $jamMasuk,
                ]);
            }
        }
        // Setelah data absensi berhasil diimpor, update jadwal karyawan
        $this->updateJadwalKaryawanWithAbsensi();

        fclose($handle); // Menutup file setelah selesai dibaca

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
