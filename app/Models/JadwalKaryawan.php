<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalKaryawan extends Model
{
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
}
