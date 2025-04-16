<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanCuti;
use App\Models\JenisCuti;
use App\Models\JadwalKaryawan;
use Carbon\Carbon;

class PengajuanCutiController extends Controller
{
    public function index()
    {
        return view('cuti.index');
    }

    public function create()
    {
        $jenisCuti = JenisCuti::all();
        return view('cuti.create', compact('jenisCuti'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_cuti_id' => 'required|exists:jenis_cuti,id',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        PengajuanCuti::create([
            'user_id' => auth()->id(),
            'jenis_cuti_id' => $request->jenis_cuti_id,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'status' => 'pending',
        ]);

        return redirect()->route('cuti.index')->with('success', 'Pengajuan cuti berhasil dikirim.');
    }

    public function approvalIndex()
    {
        $pengajuanCuti = PengajuanCuti::with('users', 'jenis_cuti')->orderBy('created_at', 'desc')->get();
        return view('cuti.approval', compact('pengajuanCuti'));
    }

    public function approve($id)
    {
        $cuti = PengajuanCuti::findOrFail($id);
        $cuti->status = 'disetujui admin';
        $cuti->save();

        // Ganti shift di tabel jadwal
        if ($cuti->jenis_cuti->status == 0) {
            $shiftId = 9998;
        } elseif ($cuti->jenis_cuti->status == 0.5) {
            $shiftId = 9996;
        } else {
            $shiftId = 9997;
        }

        JadwalKaryawan::updateOrCreate(
            ['user_id' => $cuti->user_id, 'tanggal' => $cuti->tanggal],
            ['shift_id' => $shiftId]
        );

        return back()->with('success', 'Cuti telah disetujui dan jadwal shift diperbarui.');
    }

    public function spvapprove($id)
    {
        $cuti = PengajuanCuti::findOrFail($id);
        $cuti->status = 'disetujui spv';
        $cuti->save();

        return back()->with('success', 'Cuti telah disetujui');
    }

    public function cancel($id)
    {
        $cuti = PengajuanCuti::findOrFail($id);
        $cuti->status = 'batal';
        $cuti->save();

        //Kondisi shift/libur
        if (Carbon::parse($cuti->tanggal)->isSunday()) {
            $shiftId = 9999;
        } else {
            $shiftId = $cuti->users->default_shift_id;
        }

        // Ganti shift di tabel jadwal
        JadwalKaryawan::updateOrCreate(
            ['user_id' => $cuti->user_id, 'tanggal' => $cuti->tanggal],
            ['shift_id' => $shiftId]
        );

        return back()->with('success', 'Cuti telah disetujui dan jadwal shift diperbarui.');
    }

    public function reject($id)
    {
        $cuti = PengajuanCuti::findOrFail($id);
        $cuti->status = 'ditolak';
        $cuti->save();

        return back()->with('success', 'Cuti telah ditolak.');
    }
}
