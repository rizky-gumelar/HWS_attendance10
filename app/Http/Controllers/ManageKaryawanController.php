<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ManageKaryawanController extends Controller
{
    public function index()
    {
        $karyawans = User::all();
        return view('manage-karyawan.index', compact('karyawans'));
    }

    public function create()
    {
        return view('manage-karyawan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_karyawan'  => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8', // Perbaikan validasi password
            'toko_id' => 'required|exists:toko,id',
            'divisi' => 'required',
            'no_hp' => 'nullable|numeric', // Boleh kosong, tetapi harus angka jika diisi
            'role' => 'required|in:admin,spv,karyawan',
        ]);

        User::create([
            'nama_karyawan' => $request->nama_karyawan,
            'email' => $request->email,
            'password' => bcrypt($request->password), // Simpan password dengan hashing
            'toko_id' => $request->toko_id,
            'divisi' => $request->divisi,
            'no_hp' => $request->no_hp,
            'role' => $request->role,
            'total_cuti' => 0, // Set default ke 0
        ]);

        return redirect()->route('manage-karyawan.index')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function edit(User $karyawan)
    {
        return view('manage-karyawan.edit', compact('karyawan'));
    }

    public function update(Request $request, User $karyawan)
    {
        $request->validate([
            'toko_id' => 'required|exists:toko,id',
            'nama_karyawan' => 'required|string|max:255',
            'divisi' => 'required|string|max:255',
            'no_hp' => 'required|numeric|unique:users,no_hp,' . $karyawan->id, // Unik kecuali untuk user ini
            'email' => 'required|email|unique:users,email,' . $karyawan->id,
            'password' => 'nullable|min:8', // Opsional, hanya diupdate jika diisi
            'role' => 'required|in:admin,spv,karyawan',
        ]);

        // Update data karyawan
        $karyawan->update([
            'toko_id' => $request->toko_id,
            'nama_karyawan' => $request->nama_karyawan,
            'divisi' => $request->divisi,
            'no_hp' => $request->no_hp,
            'email' => $request->email,
            'role' => $request->role,
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
}
