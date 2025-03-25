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
}
