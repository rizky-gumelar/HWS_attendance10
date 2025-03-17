<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanMingguan extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'minggu_ke',
        'nama_karyawan',
        'toko',
        'total_kehadiran',
        'total_terlambat',
        'total_lembur',
        'uang_mingguan',
        'uang_kedatangan',
        'uang_lembur_mingguan',
    ];
}
