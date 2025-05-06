<?php

namespace App\Http\Controllers;

use App\Models\RekapTahunan;
use App\Models\Keuangan;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Setting;

class AdminController extends Controller
{
    public function index()
    {
        // $toleransi = 15;

        $divisiList = Divisi::all();
        return view('dashboard.admin', compact('divisiList'));
        // return view('dashboard.admin');
    }

    public function update(Request $request)
    {
        $request->validate([
            'toleransi_masuk' => 'required|integer|min:0|max:120',
        ]);

        Setting::updateOrCreate(
            ['key' => 'toleransi_masuk'],
            ['value' => $request->input('toleransi_masuk'), 'type' => 'int']
        );

        return redirect()->route('pengaturan.index')->with('success', 'Toleransi berhasil diperbarui.');
    }

    public function pengaturan()
    {
        $toleransi = Setting::where('key', 'toleransi_masuk')->value('value');
        $keuangan = Keuangan::first();
        return view('pengaturan_view.index', compact('keuangan', 'toleransi'));
    }
}
