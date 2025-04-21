<?php

namespace App\Http\Controllers;

use App\Models\Libur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;

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

    public function import(Request $request)
    {
        // Validasi file CSV yang di-upload
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt,xlsx|max:10240', // Maksimal 10MB
        ]);

        // Ambil file CSV yang di-upload
        $file = $request->file('csv_file');
        $filePath = $file->getRealPath();

        // Baca file CSV menggunakan PhpSpreadsheet
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Ambil data dari sheet (dimulai dari baris kedua untuk mengabaikan header)
        $csvData = $sheet->toArray(null, true, true, true); // toArray mengubah ke array

        // Skip header jika ada
        $header = array_shift($csvData);

        // Validasi dan import data dari CSV
        foreach ($csvData as $row) {
            // Misalnya format CSV: nama_user, nama_shift, tanggal
            $tanggal = $row['A']; // Kolom A untuk nama_user
            $keterangan = $row['B']; // Kolom B untuk nama_shift

            // Cek apakah user dan shift ada di database
            $tanggallibur = Libur::where('tanggal', $tanggal)->first();

            // Validasi tanggal
            $validator = Validator::make(['tanggal' => $tanggal], [
                'tanggal' => 'required|date',
            ]);

            if ($validator->fails()) {
                dd("Tanggal tidak valid: $tanggal");
            }

            if (!$tanggallibur) {
                // Validasi tanggal
                $validator = Validator::make(['tanggal' => $tanggal], [
                    'tanggal' => 'required',
                ]);
                if ($validator->fails()) {
                    continue; // Skip jika ada tanggal yang tidak valid
                }
                Libur::create([
                    'tanggal' => $tanggal,
                    'keterangan' => $keterangan,
                ]);
            } else {
                $tanggallibur->update([
                    'tanggal' => $tanggal,
                    'keterangan' => $keterangan,
                ]);
            }
        }

        return redirect()->route('libur.index')->with('success', 'Libur berhasil diimpor.');
    }
}
