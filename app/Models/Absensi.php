<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'absensi_harian';
    use HasFactory;
    protected $fillable = [
        'user_id',
        'tanggal',
        'jam_masuk',
    ];

    public function jadwalKaryawan()
    {
        return $this->hasMany(JadwalKaryawan::class);
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
