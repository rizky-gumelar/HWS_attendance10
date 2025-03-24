<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lembur extends Model
{
    protected $table = 'lembur';
    use HasFactory;
    protected $fillable = [
        'tipe_lembur',
        'biaya',
    ];

    // Relasi ke JadwalKaryawan
    public function jadwalKaryawan()
    {
        return $this->hasMany(JadwalKaryawan::class);
    }
}
