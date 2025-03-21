<?php

namespace App\Http\Controllers;

use App\Models\JenisCuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JenisCutiController extends Controller
{
    public function index()
    {
        $jeniscutis = JenisCuti::all();
        return view('jeniscuti_view.index', compact('jeniscutis'));
    }

    public function create()
    {
        return view('jeniscuti_view.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_cuti'  => 'required|string|max:255',
            'status' => 'required',
        ]);

        JenisCuti::create([
            'nama_cuti' => $request->nama_cuti,
            'status' => $request->status,
        ]);

        return redirect()->route('jenis-cuti.index')->with('success', 'jenis cuti berhasil ditambahkan.');
    }

    public function edit(JenisCuti $jeniscuti)
    {
        return view('jeniscuti_view.edit', compact('jeniscuti'));
    }

    public function update(Request $request, JenisCuti $jeniscuti)
    {
        try {
            $request->validate([
                'nama_cuti' => 'required|string|max:255',
                'status' => 'required',
            ]);

            $jeniscuti->update([
                'nama_cuti' => $request->nama_cuti,
                'status' => $request->status,
            ]);

            // Redirect ke halaman jenis cuti dan beri pesan sukses
            return redirect()->route('jenis-cuti.index')->with('success', 'jenis cuti berhasil diperbarui!');
        } catch (\Exception $e) {
            // Log error for debugging
            Log::error('Error updating jenis cuti: ' . $e->getMessage());
            return redirect()->route('jenis-cuti.index')->with('error', 'Failed to update jenis-cuti.');
        }
    }

    public function destroy(JenisCuti $jenis_cuti)
    {
        $jenis_cuti->delete();
        return redirect()->route('jenis-cuti.index')->with('success', 'jenis cuti deleted successfully.');
    }
}
