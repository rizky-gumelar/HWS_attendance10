<?php

namespace App\Http\Controllers;

use App\Models\Keuangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KeuanganController extends Controller
{
    public function index()
    {
        $keuangan = Keuangan::first();
        return view('keuangan_view.index', compact('keuangan'));
    }

    public function edit(Keuangan $keuangan)
    {
        return view('keuangan_view.edit', compact('keuangan'));
    }

    public function update(Request $request, Keuangan $keuangan)
    {
        try {
            $request->validate([
                'uang_mingguan' => 'required|numeric',
                'uang_kedatangan' => 'required|numeric',
            ]);

            $keuangan->update([
                'uang_mingguan' => $request->uang_mingguan,
                'uang_kedatangan' => $request->uang_kedatangan,
            ]);

            // Redirect ke halaman keuangan dan beri pesan sukses
            return redirect()->route('keuangan.index')->with('success', 'keuangan berhasil diperbarui!');
        } catch (\Exception $e) {
            // Log error for debugging
            Log::error('Error updating keuangan: ' . $e->getMessage());
            return redirect()->route('keuangan.index')->with('error', 'Failed to update keuangan.');
        }
    }
}
