<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\DB;
use App\Models\Lembur;
use App\Models\JadwalKaryawan;

class ManageLemburController extends Controller
{
    public function index()
    {
        $lemburs = Lembur::all();
        return view('lembur_view.index', compact('lemburs'));
    }

    public function create()
    {
        return view('lembur_view.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe_lembur'  => 'required|string|max:255',
            'biaya' => 'required|numeric',
        ]);

        Lembur::create([
            'tipe_lembur' => $request->tipe_lembur,
            'biaya' => $request->biaya,
        ]);

        return redirect()->route('lembur.index')->with('success', 'Lembur berhasil ditambahkan.');
    }

    public function edit(Lembur $lembur)
    {
        return view('lembur_view.edit', compact('lembur'));
    }

    public function update(Request $request, Lembur $lembur)
    {
        try {
            $request->validate([
                'tipe_lembur'  => 'required|string|max:255',
                'biaya' => 'required|numeric',
            ]);
            $lembur->update([
                'tipe_lembur' => $request->tipe_lembur,
                'biaya' => $request->biaya,
            ]);
            // Redirect ke halaman shift dan beri pesan sukses
            return redirect()->route('lembur.index')->with('success', 'lembur berhasil diperbarui!');
        } catch (\Exception $e) {
            // Log error for debugging
            Log::error('Error updating lembur: ' . $e->getMessage());
            return redirect()->route('lembur.index')->with('error', 'Failed to update lembur.');
        }
    }

    public function destroy(Lembur $lembur)
    {
        $lembur->delete();
        return redirect()->route('lembur.index')->with('success', 'lembur deleted successfully.');
    }

    public function import()
    {
        // $lemburs = Lembur::all();
        // Ambil data dan kelompokkan berdasarkan tanggal dan lembur_id
        $grouplembur = JadwalKaryawan::with('lembur')
            ->select('tanggal', 'lembur_id', DB::raw('SUM(total_lembur) as total'))
            ->whereNotNull('lembur_id')
            ->groupBy('tanggal', 'lembur_id')
            ->orderBy('tanggal', 'desc')
            ->get();
        return view('lembur_view.import', compact('grouplembur'));

        // debug
        // return response()->json([
        //     'grouplembur' => $grouplembur
        // ]);
    }


    public function getDetail(Request $request)
    {
        $tanggal = $request->input('tanggal');
        $lembur_id = $request->input('lembur_id');

        $jadwalList = JadwalKaryawan::with(['users', 'lembur']) // asumsikan ada relasi
            ->where('tanggal', $tanggal)
            ->where('lembur_id', $lembur_id)
            ->get();

        return response()->json([
            'jadwal' => $jadwalList
        ]);
    }

    //CORETAN QUERY
    // public function getDetail2(Request $request)
    // {
    //     $lembur_id = $request->input('lembur_id');

    //     $jadwalList = JadwalKaryawan::with(['users', 'lembur'])
    //         ->where('minggu_ke', $minggu_ke)
    //         ->where('lembur_id', $lembur_id)
    //         ->orderBy('tanggal', 'desc')
    //         ->get();

    //     return response()->json([
    //         'jadwal' => $jadwalList
    //     ]);
    // }
}
