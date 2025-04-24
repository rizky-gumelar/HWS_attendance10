<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanCuti;
use App\Models\JenisCuti;
use App\Models\JadwalKaryawan;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class PengajuanCutiController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('cuti.index', compact('user'));
    }

    public function create()
    {
        $jenisCuti = JenisCuti::all();
        return view('cuti.create', compact('jenisCuti'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_cuti_id' => 'required|exists:jenis_cuti,id',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
            'imagename' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imageName = null;
        if ($request->hasFile('imagename')) {
            $imageName = time() . '.' . $request->imagename->extension();
            $request->imagename->storeAs('public/cuti', $imageName);
        }

        PengajuanCuti::create([
            'user_id' => auth()->id(),
            'jenis_cuti_id' => $request->jenis_cuti_id,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'status' => 'pending',
            'imagename' => $imageName,
        ]);

        return redirect()->route('cuti.index')->with('success', 'Pengajuan cuti berhasil dikirim.');
    }

    public function approvalIndex()
    {
        $user = auth()->user();
        $query = PengajuanCuti::with('users', 'jenis_cuti')->orderBy('created_at', 'desc');

        if ($user->role === 'spv') {
            $query->whereHas('users', function ($q) use ($user) {
                $q->where('divisi_id', $user->divisi_id)->where('role', '!=', 'admin');
            });
        }
        // Ambil data yang sudah difilter
        $pengajuanCuti = $query->get();
        return view('cuti.approval', compact('pengajuanCuti'));
    }

    public function approve($id)
    {
        $cuti = PengajuanCuti::findOrFail($id);
        $cuti->status = 'disetujui admin';
        $cuti->save();

        // Ganti shift di tabel jadwal
        if ($cuti->jenis_cuti->status == 0) {
            $shiftId = 9998;
        } elseif ($cuti->jenis_cuti->status == 0.5) {
            $shiftId = 9996;
        } else {
            $shiftId = 9997;
        }

        $absensi = Absensi::create([
            'user_id' => $cuti->user_id,
            'tanggal' => $cuti->tanggal,
            'jam_masuk' => '00:00:00',
        ]);

        JadwalKaryawan::updateOrCreate(
            [
                'user_id' => $cuti->user_id,
                'tanggal' => $cuti->tanggal,
                'minggu_ke' => Carbon::parse($cuti->tanggal)->startOfWeek(Carbon::SATURDAY)->weekOfYear,
            ],
            [
                'absen_id' => $absensi->id, // Masukkan absen_id di bagian "values",
                'shift_id' => $shiftId,
                'cek_keterlambatan' => 0,
            ]
        );

        // Kurangi total_cuti user
        $user = $cuti->users;
        $user->total_cuti = $user->total_cuti - $cuti->jenis_cuti->status;
        $user->save();

        return back()->with('success', 'Cuti telah disetujui dan jadwal shift diperbarui.');
    }

    public function spvapprove($id)
    {
        $cuti = PengajuanCuti::findOrFail($id);
        $cuti->status = 'disetujui spv';
        $cuti->save();

        return back()->with('success', 'Cuti telah disetujui');
    }

    public function cancel($id)
    {
        $cuti = PengajuanCuti::findOrFail($id);
        $cuti->status = 'batal';
        $cuti->save();

        //Kondisi shift/libur
        if (Carbon::parse($cuti->tanggal)->isSunday()) {
            $shiftId = 9999;
        } else {
            $shiftId = $cuti->users->default_shift_id;
        }

        // Ganti shift di tabel jadwal
        JadwalKaryawan::updateOrCreate(
            ['user_id' => $cuti->user_id, 'tanggal' => $cuti->tanggal],
            [
                'shift_id' => $shiftId,
                'absen_id' => null,
                'cek_keterlambatan' => 2,
            ]
        );

        // Kurangi total_cuti user
        $user = $cuti->users;
        $user->total_cuti = $user->total_cuti + $cuti->jenis_cuti->status;
        $user->save();

        return back()->with('success', 'Cuti telah disetujui dan jadwal shift diperbarui.');
    }

    public function reject($id)
    {
        $cuti = PengajuanCuti::findOrFail($id);
        $cuti->status = 'ditolak';
        $cuti->save();

        return back()->with('success', 'Cuti telah ditolak.');
    }

    public function show($id)
    {
        $cuti = PengajuanCuti::with('user', 'jenisCuti')->findOrFail($id);
        return view('pengajuan_cuti.show', compact('cuti'));
    }

    // public function destroy($id)
    // {
    //     $cuti = PengajuanCuti::findOrFail($id);
    //     if ($cuti->imagename) {
    //         Storage::delete('public/cuti/' . $cuti->imagename);
    //     }
    //     $cuti->delete();

    //     return redirect()->route('pengajuan_cuti.index')->with('success', 'Pengajuan cuti berhasil dihapus.');
    // }
}
