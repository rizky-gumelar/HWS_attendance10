<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanCuti extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'jenis_cuti_id',
        'tanggal',
        'keterangan',
        'status',
    ];
}
