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
        'tanggal_masuk',
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

    public function hitungPoin($tahun)
    {
        $totalBobotCuti = $this->pengajuan_cuti()
            ->where('status', 'disetujui admin')
            ->with('jenis_cuti')  // pastikan relasi diload
            ->whereYear('tanggal', $tahun)
            ->get()
            ->sum(function ($cuti) {
                $status = $cuti->jenis_cuti->status;

                // Mengubah nilai status sesuai dengan ketentuan
                if ($status == 0 || $status == 1) {
                    return 1;
                } elseif ($status == 0.5) {
                    return 0.5;
                }

                // Jika status lain yang tidak diharapkan, kembalikan 0
                return 0;
            });
        $terlambatCount = $this->jadwal_karyawan()->where('cek_keterlambatan', 1)->count();  // Menghitung keterlambatan
        // dd($terlambatCount);

        $poinSetelahCuti = $this->poin_tidak_hadir - $totalBobotCuti;  // Poin setelah pengurangan cuti
        $poinAkhir = $poinSetelahCuti - ($terlambatCount * 0.5);  // Poin setelah pengurangan keterlambatan

        return $poinAkhir;  // Pastikan poin tidak kurang dari 0
    }

    public function hitungCuti($tahun)
    {
        $totalBobotCuti = $this->pengajuan_cuti()
            ->where('status', 'disetujui admin')
            ->with('jenis_cuti')
            ->whereYear('tanggal', $tahun) // pastikan relasi diload
            ->get()
            ->sum(function ($cuti) {
                return $cuti->jenis_cuti->status;  // asumsi kolom bobot bernama 'status' di jenis_cuti
            });

        $poinSetelahCuti = $this->total_cuti - $totalBobotCuti;

        return $poinSetelahCuti;  // Pastikan poin tidak kurang dari 0
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
