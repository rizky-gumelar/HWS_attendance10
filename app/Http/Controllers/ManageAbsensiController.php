<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\Request;

class ManageAbsensiController extends Controller
{
    public function index()
    {
        $absensis = Absensi::all();
        return view('absensi_view.index', compact('absensis'));
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

    // public function destroy(Shift $shift)
    // {
    //     $shift->delete();
    //     return redirect()->route('shift.index')->with('success', 'Shift deleted successfully.');
    // }
}
