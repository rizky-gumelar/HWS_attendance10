<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekapTahunan extends Model
{
    use HasFactory;

    protected $table = 'rekap_tahunan';

    protected $fillable = [
        'id',
        'user_id',
        'tahun',
        'cuti',
        'cf',
        'sakit',
        'setengah_hari',
        'terlambat',
        'saldo_cuti',
        'poin_ketidakhadiran',
        'cuti_terpakai',
        'poin_terpakai',
        'cuti_akhir',
        'poin_akhir',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
