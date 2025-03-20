<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;

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
            'nama_shift'  => 'required|string|max:255',
            'shift_masuk' => 'required|date_format:H:i',
            'shift_keluar' => 'required|date_format:H:i|after:shift_masuk',
        ]);

        Shift::create([
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
        $request->validate([
            'nama_shift' => 'required|string|max:255',
            'shift_masuk' => 'required|date_format:H:i',
            'shift_keluar' => 'required|date_format:H:i|after:shift_masuk',
        ]);

        $shift->update([
            'nama_shift' => $request->nama_shift,
            'shift_masuk' => $request->shift_masuk,
            'shift_keluar' => $request->shift_keluar,
        ]);

        // Redirect ke halaman shift dan beri pesan sukses
        return redirect()->route('shift.index')->with('success', 'Shift berhasil diperbarui!');
    }

    public function destroy(Shift $shift)
    {
        $shift->delete();
        return redirect()->route('shift.index')->with('success', 'Employee deleted successfully.');
    }
}
