<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ManageShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::all();
        return view('shift_view.index', compact('shifts'));
    }

    public function create()
    {
        return view('shift_view.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => 'nullable|numeric',
            'nama_shift'  => 'required|string|max:255',
            'shift_masuk' => 'required',
            'shift_keluar' => 'required|after:shift_masuk',
        ]);

        $id = $request->id;

        // Jika tidak ada ID dari request, cari ID terbesar di bawah 9000 lalu tambah 1
        if (!$id) {
            $id = Shift::where('id', '<', 900)->max('id') + 1;
            if (!$id) {
                $id = 1; // fallback jika tabel masih kosong
            }
        }

        Shift::create([
            'id' => $id,
            'nama_shift' => $request->nama_shift,
            'shift_masuk' => $request->shift_masuk,
            'shift_keluar' => $request->shift_keluar,
        ]);

        return redirect()->route('shift.index')->with('success', 'Shift berhasil ditambahkan.');
    }

    public function edit(Shift $shift)
    {
        return view('shift_view.edit', compact('shift'));
    }

    public function update(Request $request, Shift $shift)
    {
        try {
            $request->validate([
                'id' => 'nullable|numeric',
                'nama_shift' => 'required|string|max:255',
                'shift_masuk' => 'required',
                'shift_keluar' => 'required|after:shift_masuk',
            ]);

            $shift->update([
                'id' => $request->id,
                'nama_shift' => $request->nama_shift,
                'shift_masuk' => $request->shift_masuk,
                'shift_keluar' => $request->shift_keluar,
            ]);

            // Redirect ke halaman shift dan beri pesan sukses
            return redirect()->route('shift.index')->with('success', 'Shift berhasil diperbarui!');
        } catch (\Exception $e) {
            // Log error for debugging
            Log::error('Error updating shift: ' . $e->getMessage());
            return redirect()->route('shift.index')->with('error', 'Failed to update shift.');
        }
    }

    public function destroy(Shift $shift)
    {
        $shift->delete();
        return redirect()->route('shift.index')->with('success', 'Shift deleted successfully.');
    }
}
