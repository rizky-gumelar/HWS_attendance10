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
        'id',
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
        'poin_tidak_hadir',
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

    public function hitungPoin()
    {
        $cutiCount = $this->pengajuan_cuti()->where('status', 'disetujui admin')->count();  // Menghitung jumlah cuti yang disetujui
        $terlambatCount = $this->jadwal_karyawan()->where('cek_keterlambatan', 1)->count();  // Menghitung keterlambatan
        // dd($terlambatCount);

        $poinSetelahCuti = 24 - $cutiCount;  // Poin setelah pengurangan cuti
        $poinAkhir = $poinSetelahCuti - ($terlambatCount * 0.5);  // Poin setelah pengurangan keterlambatan

        return max($poinAkhir, 0);  // Pastikan poin tidak kurang dari 0
    }

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
        return $this->hasMany(JadwalKaryawan::class, 'user_id');
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

    // Relasi ke tabel pengajuan_cuti
    public function pengajuan_cuti()
    {
        return $this->hasMany(PengajuanCuti::class, 'user_id');
    }
    // public function index()
    // {
    //     // Ambil seluruh user beserta nama toko mereka
    //     $users = User::with('toko')->get();

    //     // Kirim data ke view
    //     return view('manage-karyawan.index', compact('users'));
    // }
}
