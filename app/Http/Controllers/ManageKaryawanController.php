<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Toko;
use App\Models\Shift;
use App\Models\Divisi;

class ManageKaryawanController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        // $karyawans = User::where('status', 'aktif')->get();
        if ($user->role === 'admin') {
            $karyawans = User::all(); // atau pakai filter status jika perlu
        } else if ($user->role === 'spv') {
            // $karyawans = User::role(['spv', 'karyawan'])->divisi($user->divisi_id)->get();
            $karyawans = User::where('divisi_id', $user->divisi_id)
                ->where('role', '!=', 'admin')
                // ->where('id', '!=', $user->id) // kalau kamu ingin SPV tidak melihat dirinya sendiri
                ->get();
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
            'nama_karyawan'  => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8', // Perbaikan validasi password
            'toko_id' => 'required|exists:toko,id',
            'default_shift_id' => 'exists:shift,id',
            'divisi_id' => 'required|exists:divisi,id',
            'no_hp' => 'nullable|numeric', // Boleh kosong, tetapi harus angka jika diisi
            'role' => 'required|in:admin,spv,karyawan',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        User::create([
            'nama_karyawan' => $request->nama_karyawan,
            'email' => $request->email,
            'password' => bcrypt($request->password), // Simpan password dengan hashing
            'toko_id' => $request->toko_id,
            'default_shift_id' => $request->default_shift_id,
            'divisi_id' => $request->divisi_id,
            'no_hp' => $request->no_hp,
            'role' => $request->role,
            'total_cuti' => 0, // Set default ke 0
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
            'toko_id' => 'exists:toko,id',
            'default_shift_id' => 'exists:shift,id',
            'nama_karyawan' => 'required|string|max:255',
            'divisi_id' => 'required|string|max:255',
            'no_hp' => 'required|numeric|unique:users,no_hp,' . $karyawan->id, // Unik kecuali untuk user ini
            'email' => 'required|email|unique:users,email,' . $karyawan->id,
            'password' => 'nullable|min:8', // Opsional, hanya diupdate jika diisi
            'role' => 'required|in:admin,spv,karyawan',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        // Update data karyawan
        $karyawan->update([
            'toko_id' => $request->toko_id,
            'default_shift_id' => $request->default_shift_id,
            'nama_karyawan' => $request->nama_karyawan,
            'divisi_id' => $request->divisi_id,
            'no_hp' => $request->no_hp,
            'email' => $request->email,
            'role' => $request->role,
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
        $user = auth()->user();
        return view('manage-karyawan_view.ubah_password', compact('user'));
    }

    public function updatePassword(Request $request, User $karyawan)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $karyawan->update([
            'password' => bcrypt($request->password),
        ]);

        $user = auth()->user()->role;
        return redirect()->route('dashboard.' . $user)->with('success', 'Password berhasil diperbarui.');
    }
}
