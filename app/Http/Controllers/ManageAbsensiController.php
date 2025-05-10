<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Shift;
use App\Models\User;
use App\Models\JadwalKaryawan;
use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;

class ManageAbsensiController extends Controller
{
    public function index(Request $request)
    {
        $query = Absensi::with(['users']);
        // Ambil nilai rentang tanggal dari request
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // Jika ada rentang tanggal, filter berdasarkan tanggal
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        } elseif (!$startDate || !$endDate) {
            $startDate = Carbon::now()->subMonth()->startOfMonth()->toDateString(); // Start of the previous month
            $endDate = Carbon::now()->endOfMonth()->toDateString(); // End of the current month
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }
        // Ambil data yang sudah difilter
        $absensis = $query->get();

        return view('absensi_view.index', compact('absensis', 'startDate', 'endDate'));

        // $absensis = Absensi::all();
        // return view('absensi_view.index', compact('absensis'));
    }

    public function import(Request $request)
    {
        $errors = [];

        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls,dat|max:10240',
        ]);

        $file = $request->file('file');
        $filePath = $file->getRealPath();
        $extension = $file->getClientOriginalExtension();

        $rows = [];

        if (in_array($extension, ['dat', 'txt'])) {
            $handle = fopen($filePath, 'r');
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    $rows[] = explode("\t", trim($line));
                }
                fclose($handle);
            } else {
                return back()->with('error', 'Gagal membuka file.');
            }
        } else {
            try {
                $spreadsheet = IOFactory::load($filePath);
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray(null, true, true, true);
                $rows = array_map("array_values", $rows);
            } catch (\Exception $e) {
                return back()->with('error', 'File tidak dapat dibaca. Pastikan formatnya benar.');
            }
        }

        $header = array_shift($rows);

        $mingguKe = Carbon::now()->weekOfYear;
        JadwalKaryawan::where('minggu_ke', $mingguKe)->update(['absen_id' => null]);

        $importedAbsensi = [];

        foreach ($rows as $row) {
            $userId = trim(str_replace("\xEF\xBB\xBF", '', $row[0] ?? ''));
            $tanggalJamMasuk = $row[1] ?? '';

            if (!$userId || !$tanggalJamMasuk) continue;

            try {
                $tanggal = Carbon::parse(explode(' ', $tanggalJamMasuk)[0])->format('Y-m-d');
                $jamMasuk = explode(' ', $tanggalJamMasuk)[1] ?? null;
            } catch (\Exception $e) {
                $errors[] = "Format tanggal tidak valid: " . $tanggalJamMasuk;
                continue;
            }

            $user = User::find($userId);
            if (!$user) {
                $errors[] = "User ID tidak ditemukan: $userId";
                continue;
            }

            if (!$jamMasuk) {
                $errors[] = "Jam masuk kosong atau tidak valid untuk user ID: $userId";
                continue;
            }

            $absensi = Absensi::updateOrCreate(
                ['user_id' => $userId, 'tanggal' => $tanggal],
                ['jam_masuk' => $jamMasuk]
            );

            $absensi->setRelation('users', $user); // agar tidak query ulang nanti
            $importedAbsensi[] = $absensi;
        }

        // Kirim hanya absensi yang baru diimpor
        $this->updateJadwalKaryawanWithAbsensi(collect($importedAbsensi));

        if (!empty($errors)) {
            return redirect()->back()
                ->with('success', 'Data berhasil diimpor sebagian.')
                ->withErrors($errors);
        }

        return redirect()->back()->with('success', 'Data berhasil diimpor!');
    }


    private function updateJadwalKaryawanWithAbsensi(Collection $absensi)
    {
        $toleransi = Setting::where('key', 'toleransi_masuk')->value('value');

        foreach ($absensi as $item) {
            $jadwalKaryawan = JadwalKaryawan::where('user_id', $item->user_id)
                ->where('tanggal', $item->tanggal)
                ->first();

            $shift = null;

            if ($jadwalKaryawan && $jadwalKaryawan->shift_id) {
                $shift = Shift::find($jadwalKaryawan->shift_id);
            } else {
                $shift = Shift::find($item->users->default_shift_id);
            }

            if ($shift && $shift->shift_masuk) {
                $shiftMasuk = Carbon::parse($shift->shift_masuk);
                $jamMasuk = Carbon::parse($item->jam_masuk);
                $shiftMasukWithTolerance = $shiftMasuk->copy()->addMinutes($toleransi);
                $cekKeterlambatan = $jamMasuk->gt($shiftMasukWithTolerance);

                if ($jadwalKaryawan) {
                    $jadwalKaryawan->update([
                        'absen_id' => $item->id,
                        'cek_keterlambatan' => $cekKeterlambatan,
                    ]);
                } else {
                    JadwalKaryawan::create([
                        'user_id' => $item->user_id,
                        'shift_id' => $shift->id,
                        'absen_id' => $item->id,
                        'cek_keterlambatan' => $cekKeterlambatan,
                        'tanggal' => Carbon::parse($item->tanggal),
                        'minggu_ke' => Carbon::parse($item->tanggal)->startOfWeek(Carbon::SATURDAY)->weekOfYear,
                    ]);
                }
            }
        }
    }


    public function create()
    {
        $users = User::orderBy('nama_karyawan')->get();
        return view('absensi_view.create', compact('users'));
    }

    public function store(Request $request)
    {
        // Cek apakah jadwal sudah ada untuk user_id, shift_id, dan tanggal
        $existingSchedule = Absensi::where('user_id', $request->user_id)
            ->whereDate('tanggal', $request->tanggal)
            ->first();
        if ($existingSchedule) {
            // Jika jadwal sudah ada, kembalikan dengan pesan error
            return redirect()->back()->with('error', 'Absen sudah ada untuk user dan tanggal tersebut.');
        }

        $request->validate([
            'user_id'  => 'required|exists:users,id',
            'tanggal' => 'required|date',
            'jam_masuk' => 'required',
        ]);

        $absensi = Absensi::create([
            'user_id' => $request->user_id,
            'tanggal' => $request->tanggal,
            'jam_masuk' => $request->jam_masuk,
        ]);

        $absensi->setRelation('users', $absensi->user); // preload user untuk efisiensi
        $this->updateJadwalKaryawanWithAbsensi(collect([$absensi]));

        return redirect()->route('absensi.index')->with('success', 'Absensi berhasil ditambahkan.');
    }

    public function edit(Absensi $absensi)
    {
        $users = User::all();
        return view('absensi_view.edit', compact('absensi', 'users'));
    }

    public function update(Request $request, Absensi $absensi)
    {
        try {
            $request->validate([
                'user_id'  => 'required|exists:users,id',
                'tanggal' => 'required|date',
                'jam_masuk' => 'required',
            ]);

            $absensi->update([
                'user_id' => $request->user_id,
                'tanggal' => $request->tanggal,
                'jam_masuk' => $request->jam_masuk,
            ]);

            // Preload user relasi agar tidak query ulang
            $absensi->setRelation('users', $absensi->user);

            // Proses hanya absensi yang baru di-update
            $this->updateJadwalKaryawanWithAbsensi(collect([$absensi]));

            return redirect()->route('absensi.index')->with('success', 'Absensi berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('Error updating absensi: ' . $e->getMessage());
            return redirect()->route('absensi.index')->with('error', 'Gagal memperbarui absensi.');
        }
    }


    public function destroy(Absensi $absensi)
    {
        $absensi->delete();
        return redirect()->route('absensi.index')->with('success', 'Absen berhasil dihapus.');
    }
}
