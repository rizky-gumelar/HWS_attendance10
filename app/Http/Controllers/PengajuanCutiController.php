<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanCuti;
use App\Models\JenisCuti;
use App\Models\JadwalKaryawan;

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
        $cuti->status = 'approve';
        $cuti->save();

        // Ganti shift di tabel jadwal
        $shiftId = $cuti->jenis_cuti->status == 0 ? 9998 : 9997;

        JadwalKaryawan::updateOrCreate(
            ['user_id' => $cuti->user_id, 'tanggal' => $cuti->tanggal],
            ['shift_id' => $shiftId]
        );

        return back()->with('success', 'Cuti telah disetujui dan jadwal shift diperbarui.');
    }

    public function reject($id)
    {
        $cuti = PengajuanCuti::findOrFail($id);
        $cuti->status = 'reject';
        $cuti->save();

        return back()->with('success', 'Cuti telah ditolak.');
    }
}
