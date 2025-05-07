<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekapTahunan extends Model
{
    use HasFactory;

    protected $table = 'rekap_tahunan';

    // Tentukan kolom primary key yang digunakan
    protected $primaryKey = ['user_id', 'tahun']; // Kombinasi user_id dan tahun sebagai primary key

    // Karena kita tidak menggunakan auto-increment pada primary key, set incrementing ke false
    public $incrementing = false;

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
        return $this->belongsTo(User::class, 'user_id');
    }
}
