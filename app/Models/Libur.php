<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Libur extends Model
{
    use HasFactory;
    protected $fillable = ['tanggal', 'keterangan'];

    public static function isLibur($tanggal)
    {
        return self::where('tanggal', $tanggal)->exists();
    }

    public static function getLibur($tanggal)
    {
        return self::where('tanggal', $tanggal)->first();
    }
}
