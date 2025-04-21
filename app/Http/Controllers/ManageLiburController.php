<?php

namespace App\Http\Controllers;

use App\Models\Libur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ManageLiburController extends Controller
{
    public function index()
    {
        $liburs = Libur::all();
        return view('libur_view.index', compact('liburs'));
    }

    public function create()
    {
        return view('libur_view.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'keterangan'  => 'required|string|max:255',
        ]);

        Libur::create([
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('libur.index')->with('success', 'libur berhasil ditambahkan.');
    }

    public function edit(Libur $libur)
    {
        return view('libur_view.edit', compact('libur'));
    }

    public function update(Request $request, Libur $libur)
    {
        try {
            $request->validate([
                'tanggal' => 'required|date',
                'keterangan'  => 'required|string|max:255',
            ]);

            $libur->update([
                'tanggal' => $request->tanggal,
                'keterangan' => $request->keterangan,
            ]);

            // Redirect ke halaman Libur dan beri pesan sukses
            return redirect()->route('libur.index')->with('success', 'Libur berhasil diperbarui!');
        } catch (\Exception $e) {
            // Log error for debugging
            Log::error('Error updating Libur: ' . $e->getMessage());
            return redirect()->route('libur.index')->with('error', 'Failed to update libur.');
        }
    }

    public function destroy(Libur $libur)
    {
        $libur->delete();
        return redirect()->route('libur.index')->with('success', 'libur deleted successfully.');
    }
}
