<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuti;
use App\Models\JenisCuti;

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

        Cuti::create([
            'user_id' => auth()->id(),
            'jenis_cuti_id' => $request->jenis_cuti_id,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Pengajuan cuti berhasil dikirim.');
    }
}
