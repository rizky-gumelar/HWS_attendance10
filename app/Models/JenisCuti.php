<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisCuti extends Model
{

    protected $table = 'jenis_cuti';
    use HasFactory;
    protected $fillable = [
        'nama_cuti',
        'status',
    ];
}
