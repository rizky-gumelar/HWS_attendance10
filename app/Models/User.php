<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'toko_id',
        'default_shift_id',
        'nama_karyawan',
        'email',
        'email_verified_at',
        'password',
        'divisi_id',
        'no_hp',
        'role',
        'total_cuti',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getRoleNameAttribute()
    {
        switch ($this->role) {
            case 'admin':
                return 'Admin';
            case 'spv':
                return 'Supervisor';
            case 'karyawan':
                return 'Karyawan';
            default:
                return 'Unknown';
        }
    }

    public function toko()
    {
        return $this->belongsTo(Toko::class, 'toko_id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'default_shift_id');
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id');
    }

    // Relasi ke tabel jadwal_karyawan
    public function jadwal_karyawan()
    {
        return $this->hasMany(JadwalKaryawan::class, 'id');
    }

    // Relasi ke tabel jadwal_karyawan
    public function absensi_harian()
    {
        return $this->hasMany(Absensi::class, 'id');
    }

    // Relasi ke tabel jadwal_karyawan
    public function laporan_mingguan()
    {
        return $this->hasMany(LaporanMingguan::class, 'id');
    }

    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }
    public function scopeDivisi($query, $divisiId)
    {
        return $query->where('divisi_id', $divisiId);
    }
    // public function index()
    // {
    //     // Ambil seluruh user beserta nama toko mereka
    //     $users = User::with('toko')->get();

    //     // Kirim data ke view
    //     return view('manage-karyawan.index', compact('users'));
    // }
}
