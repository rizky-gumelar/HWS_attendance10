<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanCuti extends Model
{
    use HasFactory;
    protected $table = 'pengajuan_cuti';
    protected $fillable = [
        'user_id',
        'jenis_cuti_id',
        'tanggal',
        'keterangan',
        'status',
        'imagename',
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'pub_id');
    }

    public function jenis_cuti()
    {
        return $this->belongsTo(JenisCuti::class, 'jenis_cuti_id');
    }
}
