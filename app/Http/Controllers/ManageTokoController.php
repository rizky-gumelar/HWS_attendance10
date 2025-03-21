<?php

namespace App\Http\Controllers;

use App\Models\Toko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ManageTokoController extends Controller
{
    public function index()
    {
        $tokos = Toko::all();
        return view('toko_view.index', compact('tokos'));
    }

    public function create()
    {
        return view('toko_view.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_toko'  => 'required|string|max:255',
            'alamat' => 'required',
        ]);

        Toko::create([
            'nama_toko' => $request->nama_toko,
            'alamat' => $request->alamat,
        ]);

        return redirect()->route('toko.index')->with('success', 'toko berhasil ditambahkan.');
    }

    public function edit(Toko $toko)
    {
        return view('toko_view.edit', compact('toko'));
    }

    public function update(Request $request, Toko $toko)
    {
        try {
            $request->validate([
                'nama_toko' => 'required|string|max:255',
                'alamat' => 'required',
            ]);

            $toko->update([
                'nama_toko' => $request->nama_toko,
                'alamat' => $request->alamat,
            ]);

            // Redirect ke halaman toko dan beri pesan sukses
            return redirect()->route('toko.index')->with('success', 'toko berhasil diperbarui!');
        } catch (\Exception $e) {
            // Log error for debugging
            Log::error('Error updating toko: ' . $e->getMessage());
            return redirect()->route('toko.index')->with('error', 'Failed to update toko.');
        }
    }

    public function destroy(Toko $toko)
    {
        $toko->delete();
        return redirect()->route('toko.index')->with('success', 'toko deleted successfully.');
    }
}
