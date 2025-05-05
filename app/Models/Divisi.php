<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    public $table = 'divisi';
    use HasFactory;

    protected $fillable = ['nama_divisi', 'finger', 'mingguan', 'kedatangan'];


    // Relasi ke tabel User
    public function users()
    {
        return $this->hasMany(User::class, 'id');
    }
}
