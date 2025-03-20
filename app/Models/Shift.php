<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $table = 'shift';
    use HasFactory;
    protected $fillable = ['nama_shift', 'shift_masuk', 'shift_keluar'];
}
