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

        $query = PengajuanCuti::with('users', 'jenis_cuti')->orderBy('created_at', 'desc')->where('user_id', $user->id);

        $user->poin_terakhir = $user->hitungPoin();
        $user->sisa_cuti = $user->hitungCuti();

        // Ambil data yang sudah difilter
        $pengajuanCuti = $query->get();
        return view('cuti.index', compact('user', 'pengajuanCuti'));
    }

    public function create()
    {
        $jenisCuti = JenisCuti::all();
        return view('cuti.create', compact('jenisCuti'));
    }

    public function store(Request $request)
    {
        $user_id = auth()->user()->id;
        // Cek apakah jadwal sudah ada untuk user_id, shift_id, dan tanggal
        $existingSchedule = PengajuanCuti::where('user_id', $user_id)
            ->where('status',  '!=', 'batal')
            ->whereDate('tanggal', $request->tanggal)
            ->first();
        if ($existingSchedule) {
            // Jika jadwal sudah ada, kembalikan dengan pesan error
            return redirect()->back()->with('error', 'Sudah ada pengajuan cuti pada tanggal tersebut. Hubungi admin untuk membatalkan');
        }

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
            if ($cuti->jenis_cuti->nama_cuti == 'Sakit') {
                $shiftId = 9995;
            } else {
                $shiftId = 9998;
            }
        } elseif ($cuti->jenis_cuti->status == 0.5) {
            $shiftId = 9996;
        } else {
            $shiftId = 9997;
        }

        // $absensi = Absensi::create([
        //     'user_id' => $cuti->user_id,
        //     'tanggal' => $cuti->tanggal,
        //     'jam_masuk' => '00:00:00',
        // ]);

        JadwalKaryawan::updateOrCreate(
            [
                'user_id' => $cuti->user_id,
                'tanggal' => $cuti->tanggal,
                'minggu_ke' => Carbon::parse($cuti->tanggal)->startOfWeek(Carbon::SATURDAY)->weekOfYear,
            ],
            [
                // 'absen_id' => $absensi->id, // Masukkan absen_id di bagian "values",
                'shift_id' => $shiftId,
                'cek_keterlambatan' => 0,
            ]
        );

        // Kurangi total_cuti user
        // $user = $cuti->users;
        // $user->total_cuti = $user->total_cuti - $cuti->jenis_cuti->status;
        // if ($cuti->jenis_cuti->status == 0.5) {
        //     $user->poin_tidak_hadir = $user->poin_tidak_hadir - 0.5;
        // } else {
        //     $user->poin_tidak_hadir = $user->poin_tidak_hadir - 1;
        // }
        // $user->save();

        return back()->with('success', 'Cuti telah disetujui dan jadwal shift diperbarui.');
    }

    public function approveAll()
    {
        $cutis = PengajuanCuti::where('status', 'pending')->get();
        foreach ($cutis as $cuti) {
            $cuti->status = 'disetujui admin';
            $cuti->save();

            // Tentukan shift
            if ($cuti->jenis_cuti->status == 0) {
                $shiftId = $cuti->jenis_cuti->nama_cuti == 'Sakit' ? 9995 : 9998;
            } elseif ($cuti->jenis_cuti->status == 0.5) {
                $shiftId = 9996;
            } else {
                $shiftId = 9997;
            }

            // Update jadwal
            JadwalKaryawan::updateOrCreate(
                [
                    'user_id' => $cuti->user_id,
                    'tanggal' => $cuti->tanggal,
                    'minggu_ke' => Carbon::parse($cuti->tanggal)->startOfWeek(Carbon::SATURDAY)->weekOfYear,
                ],
                [
                    'shift_id' => $shiftId,
                    'cek_keterlambatan' => 0,
                ]
            );

            // Jika ingin update total_cuti user juga:
            // $user = $cuti->users;
            // $user->total_cuti -= $cuti->jenis_cuti->status;
            // $user->poin_tidak_hadir -= ($cuti->jenis_cuti->status == 0.5) ? 0.5 : 1;
            // $user->save();
        }

        return back()->with('success', 'Semua cuti pending telah disetujui dan jadwal diperbarui.');
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

        // Cari jadwal karyawan untuk tanggal dan user tersebut
        $jadwal = JadwalKaryawan::where('user_id', $cuti->user_id)
            ->where('tanggal', $cuti->tanggal)
            ->first();

        // // Hapus absensi jika ada
        // if ($jadwal && $jadwal->absen_id) {
        //     Absensi::where('id', $jadwal->absen_id)->delete();
        // }

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
                'minggu_ke' => Carbon::parse($cuti->tanggal)->startOfWeek(Carbon::SATURDAY)->weekOfYear,
            ]
        );

        return back()->with('success', 'Cuti telah dibatalkan dan jadwal shift dikembalikan.');
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
