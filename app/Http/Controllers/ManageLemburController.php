<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


use App\Models\Lembur;

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
        $lemburs = Lembur::all();
        return view('lembur_view.import', compact('lemburs'));
    }
}
