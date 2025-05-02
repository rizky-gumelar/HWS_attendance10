<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Toko;
use App\Models\Shift;
use App\Models\Divisi;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Artisan;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ManageKaryawanController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        // $karyawans = User::where('status', 'aktif')->get();
        if ($user->role === 'admin') {
            // $karyawans = User::all(); // atau pakai filter status jika perlu
            $karyawans = User::all()->map(function ($user) {
                // Tambahkan poin ketidakhadiran setiap karyawan
                $user->poin_terakhir = $user->hitungPoin();
                $user->sisa_cuti = $user->hitungCuti();
                return $user;
            });
        } else if ($user->role === 'spv') {
            // $karyawans = User::role(['spv', 'karyawan'])->divisi($user->divisi_id)->get();
            $karyawans = User::where('divisi_id', $user->divisi_id)
                ->where('role', '!=', 'admin')
                ->get()
                ->map(function ($user) {
                    $user->poin_terakhir = $user->hitungPoin();
                    $user->sisa_cuti = $user->hitungCuti();
                    return $user;
                });
            // ->where('id', '!=', $user->id) // kalau kamu ingin SPV tidak melihat dirinya sendiri
            // ->get();
        } else {
            // fallback jika bukan admin/spv (misal user biasa)
            abort(403, 'Unauthorized');
        }
        // $karyawans = User::orderBy('status', 'asc')->orderBy('id', 'asc')->get();
        return view('manage-karyawan_view.index', compact('karyawans'));
    }

    public function create()
    {
        $tokos = Toko::all();
        $shifts = Shift::all();
        $divisis = Divisi::all();
        return view('manage-karyawan_view.create', compact('tokos', 'shifts', 'divisis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => 'nullable|numeric',
            'nama_karyawan'  => 'required',
            'tanggal_masuk' => 'required|date',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8', // Perbaikan validasi password
            'toko_id' => 'required|exists:toko,id',
            'default_shift_id' => 'exists:shift,id',
            'divisi_id' => 'required|exists:divisi,id',
            'no_hp' => 'nullable|numeric', // Boleh kosong, tetapi harus angka jika diisi
            'role' => 'required|in:admin,spv,karyawan',
            'total_cuti' => 'nullable|numeric',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $id = $request->id;

        // Jika tidak ada ID dari request, cari ID terbesar di bawah 9000 lalu tambah 1
        if (!$id) {
            $id = Shift::where('id', '<', 900)->max('id') + 1;
            if (!$id) {
                $id = 1; // fallback jika tabel masih kosong
            }
        }

        User::create([
            'id' => $id,
            'nama_karyawan' => $request->nama_karyawan,
            'tanggal_masuk' => $request->tanggal_masuk,
            'email' => $request->email,
            'password' => bcrypt($request->password), // Simpan password dengan hashing
            'toko_id' => $request->toko_id,
            'default_shift_id' => $request->default_shift_id,
            'divisi_id' => $request->divisi_id,
            'no_hp' => $request->no_hp,
            'role' => $request->role,
            'total_cuti' => $request->total_cuti, // Set default ke 0
            'status' => 'aktif',
        ]);

        return redirect()->route('manage-karyawan.index')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function edit(User $karyawan)
    {
        $tokos = Toko::all();
        $shifts = Shift::all();
        $divisis = Divisi::all();
        return view('manage-karyawan_view.edit', compact('karyawan', 'tokos', 'shifts', 'divisis'));
    }

    public function update(Request $request, User $karyawan)
    {
        $request->validate([
            'id' => 'nullable|numeric',
            'toko_id' => 'exists:toko,id',
            'default_shift_id' => 'exists:shift,id',
            'nama_karyawan' => 'required|string|max:255',
            'tanggal_masuk' => 'required|date',
            'divisi_id' => 'required|string|max:255',
            'no_hp' => 'required|numeric|unique:users,no_hp,' . $karyawan->id, // Unik kecuali untuk user ini
            'email' => 'required|email|unique:users,email,' . $karyawan->id,
            'password' => 'nullable|min:8', // Opsional, hanya diupdate jika diisi
            'role' => 'required|in:admin,spv,karyawan',
            'total_cuti' => 'nullable|numeric',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        // Update data karyawan
        $karyawan->update([
            'id' => $request->id,
            'toko_id' => $request->toko_id,
            'default_shift_id' => $request->default_shift_id,
            'nama_karyawan' => $request->nama_karyawan,
            'tanggal_masuk' => $request->tanggal_masuk,
            'divisi_id' => $request->divisi_id,
            'no_hp' => $request->no_hp,
            'email' => $request->email,
            'role' => $request->role,
            'total_cuti' => $request->total_cuti,
            'status' => $request->status,
        ]);

        // Jika password diisi, update dengan hash baru
        if ($request->filled('password')) {
            $karyawan->update(['password' => bcrypt($request->password)]);
        }

        return redirect()->route('manage-karyawan.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(User $karyawan)
    {
        $karyawan->delete();
        return redirect()->route('manage-karyawan.index')->with('success', 'Employee deleted successfully.');
    }

    public function editPassword()
    {
        return view('manage-karyawan_view.ubah_password');
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->password = bcrypt($request->password);
        $user->save();

        $user_route = auth()->user()->role;
        return redirect()->route('dashboard.' . $user_route)->with('success', 'Password berhasil diperbarui.');
    }

    public function import(Request $request)
    {
        $errors = [];
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt,xlsx|max:10240' // Validasi file
        ]);

        if ($request->hasFile('csv_file')) {
            $file = $request->file('csv_file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            foreach ($rows as $key => $row) {
                if ($key === 0) continue; // Lewati header

                // Pastikan data minimal ada email
                if (empty($row[2])) continue;

                $tokoName = $row[4];
                $defaultShiftName = $row[5];
                $divisiName = $row[6];


                // Cek apakah user dan shift ada di database
                $toko = Toko::where('nama_toko', $tokoName)->first();
                $defaultShift = Shift::where('nama_shift', $defaultShiftName)->first();
                $divisi = Divisi::where('nama_divisi', $divisiName)->first();

                // Debug: Cek apakah user dan shift ditemukan
                if (!$toko) {
                    $errors[] = "Toko tidak ditemukan: $tokoName";
                    continue;
                }
                if (!$defaultShift) {
                    $errors[] = "Shift tidak ditemukan: $defaultShiftName";
                    continue;
                }
                if (!$divisi) {
                    $errors[] = "Divisi tidak ditemukan: $divisiName";
                    continue;
                }

                if ($toko && $defaultShift && $divisi) {
                    // Update jika email sudah ada, jika tidak maka buat baru
                    User::updateOrCreate(
                        ['email' => $row[2]], // Cek berdasarkan email

                        // Kolom yang di-update atau diisi
                        [
                            'id' => $row[0] ?? null,
                            'nama_karyawan' => $row[1] ?? null,
                            'password' => isset($row[3]) ? bcrypt($row[3]) : null,
                            'toko_id' => $toko->id ?? null,
                            'default_shift_id' => $defaultShift->id ?? null,
                            'divisi_id' => $divisi->id ?? null,
                            'no_hp' => $row[7] ?? null,
                            'role' => $row[8] ?? 'karyawan',
                            'total_cuti' => $row[9] ?? 0,
                            'tanggal_masuk' => $row[10] ?? 0,
                            'status' => 'aktif',
                        ]
                    );
                }
            }

            if (!empty($errors)) {
                return redirect()->route('lembur.import')
                    ->with('success', 'Jadwal berhasil diimpor sebagian.')
                    ->withErrors($errors);
            }

            return redirect()->route('manage-karyawan.index')->with('success', 'Data karyawan berhasil diimpor.');
        }

        return back()->with('error', 'File tidak valid!');
    }
}
