<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanMingguan extends Model
{
    public $table = 'laporan_mingguan';
    use HasFactory;
    protected $fillable = [
        'user_id',
        'minggu_ke',
        'd1',
        'd2',
        'd3',
        'd4',
        'd5',
        'd6',
        'd7',
        'uang_mingguan',
        'uang_kedatangan',
        'uang_lembur_mingguan',
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }
}
