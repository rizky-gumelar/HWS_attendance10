<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keuangan extends Model
{
    public $table = 'keuangan';
    protected $fillable = [
        'uang_mingguan',
        'uang_kedatangan',
    ];
    use HasFactory;
}
