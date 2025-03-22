<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalKaryawan extends Model
{
    protected $table = 'jadwal_karyawan';
    use HasFactory;
    protected $fillable = [
        'user_id',
        'shift_id',
        'absen_id',
        'lembur_id',
        'tanggal',
        'cek_keterlambatan',
        'lembur_jam',
        'total_lembur',
        'keterangan',
        'minggu_ke'
    ];


    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    // public function index()
    // {
    //     // Ambil seluruh user beserta nama toko mereka
    //     $input_jadwals = User::with('shift')->get();

    //     // Kirim data ke view
    //     return view('input-jadwal.index', compact('input_jadwals'));
    // }
}
