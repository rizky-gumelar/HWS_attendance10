<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ManageDivisiController extends Controller
{
    public function index()
    {
        $divisis = Divisi::all();
        return view('divisi_view.index', compact('divisis'));
    }

    public function create()
    {
        return view('divisi_view.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_divisi'  => 'required|string|max:255',
            'mingguan'  => 'required|boolean',
            'kedatangan'  => 'required|boolean',
        ]);

        Divisi::create([
            'nama_divisi' => $request->nama_divisi,
            'mingguan' => $request->mingguan,
            'kedatangan' => $request->kedatangan,
        ]);

        return redirect()->route('divisi.index')->with('success', 'Divisi berhasil ditambahkan.');
    }

    public function edit(Divisi $divisi)
    {
        return view('divisi_view.edit', compact('divisi'));
    }

    public function update(Request $request, Divisi $divisi)
    {
        try {
            $request->validate([
                'nama_divisi' => 'required|string|max:255',
                'mingguan'  => 'required|boolean',
                'kedatangan'  => 'required|boolean',
            ]);

            $divisi->update([
                'nama_divisi' => $request->nama_divisi,
                'mingguan' => $request->mingguan,
                'kedatangan' => $request->kedatangan,
            ]);

            // Redirect ke halaman Divisi dan beri pesan sukses
            return redirect()->route('divisi.index')->with('success', 'Divisi berhasil diperbarui!');
        } catch (\Exception $e) {
            // Log error for debugging
            Log::error('Error updating Divisi: ' . $e->getMessage());
            return redirect()->route('divisi.index')->with('error', 'Failed to update divisi.');
        }
    }

    public function destroy(Divisi $divisi)
    {
        $divisi->delete();
        return redirect()->route('divisi.index')->with('success', 'Divisi deleted successfully.');
    }
}
