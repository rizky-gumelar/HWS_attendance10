<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'description'];

    public $timestamps = true; // jika kamu menggunakan kolom created_at & updated_at
}
