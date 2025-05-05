<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    public $table = 'divisi';
    use HasFactory;

    protected $fillable = ['nama_divisi', 'mingguan', 'kedatangan'];


    // Relasi ke tabel User
    public function users()
    {
        return $this->hasMany(User::class, 'id');
    }

    public function getMingguanNameAttribute()
    {
        switch ($this->mingguan) {
            case '0':
                return 'tidak';
            case '1':
                return 'ya';
            default:
                return 'Unknown';
        }
    }

    public function getKedatanganNameAttribute()
    {
        switch ($this->kedatangan) {
            case '0':
                return 'tidak';
            case '1':
                return 'ya';
            default:
                return 'Unknown';
        }
    }
}
