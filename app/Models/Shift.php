<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $table = 'shift';
    use HasFactory;
    protected $fillable = ['nama_shift', 'shift_masuk', 'shift_keluar'];

    // Relasi ke tabel jadwal_karyawan
    public function jadwal_karyawan()
    {
        return $this->hasMany(JadwalKaryawan::class, 'id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'id');
    }

    public function laporan_mingguan()
    {
        return $this->hasMany(JadwalKaryawan::class, 'id');
    }
}
