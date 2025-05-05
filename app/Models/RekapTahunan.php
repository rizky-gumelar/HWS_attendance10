<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekapTahunan extends Model
{
    protected $table = 'rekap_tahunan';

    use HasFactory;
    protected $fillable = [
        'user_id',
        'tahun',
        'cuti',
        'cf',
        'sakit',
        'setengah_hari',
        'saldo_cuti',
        'poin_ketidakhadiran',
        'cuti_terpakai',
        'poin_terpakai',
        'cuti_akhir',
        'poin_akhir',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Menggunakan user_id untuk relasi
    }
}
