<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {

        User::create([
            'id' => 1,
            'toko_id' => 1,
            'nama_karyawan' => 'Admin User',
            'tanggal_masuk' => '2020-01-20',
            'default_shift_id' => 1,
            'divisi_id' => 9,
            'no_hp' => '081234567890',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'admin',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        // User::create([
        //     'id' => 2,
        //     'toko_id' => 3,
        //     'nama_karyawan' => 'DAFFA ARYA SENA HEDEN',
        //     'default_shift_id' => 1,
        //     'divisi_id' => 1,
        //     'no_hp' => '081390040402',
        //     'email' => 'daffa.arya@hws.com',
        //     'email_verified_at' => now(),
        //     'password' => Hash::make('password'),
        //     'role' => 'karyawan',
        //     'total_cuti' => 24,
        //     'status' => 'aktif',
        // ]);

        // User::create([
        //     'id' => 3,
        //     'toko_id' => 2,
        //     'nama_karyawan' => 'TYAH DIAN OCHTAVIYANA',
        //     'default_shift_id' => 1,
        //     'divisi_id' => 1,
        //     'no_hp' => '08886699074',
        //     'email' => 'tyah.dian@hws.com',
        //     'email_verified_at' => now(),
        //     'password' => Hash::make('password'),
        //     'role' => 'spv',
        //     'total_cuti' => 24,
        //     'status' => 'aktif',
        // ]);
    }

    // User::create([
    //     'toko_id' => 3,
    //     'nama_karyawan' => 'DAFFA ARYA SENA HEDEN',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 1,
    //     'no_hp' => '081390040402',
    //     'email' => 'daffa.arya@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-06072004'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 2,
    //     'nama_karyawan' => 'TYAH DIAN OCHTAVIYANA',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 1,
    //     'no_hp' => '08886699074',
    //     'email' => 'tyah.dian@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-29101993'),
    //     'role' => 'spv',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 3,
    //     'nama_karyawan' => 'NAWANGGI DWINDA ARSILA',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 2,
    //     'no_hp' => '0895360526882',
    //     'email' => 'nawanggi.dwinda@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-12072000'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 3,
    //     'nama_karyawan' => 'TONI IQBAL RAMDHANI',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 2,
    //     'no_hp' => '085155090454',
    //     'email' => 'toni.iqbal@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-24081995'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 4,
    //     'nama_karyawan' => 'M FATIH KHOIRUL IBAD',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 2,
    //     'no_hp' => '082244536790',
    //     'email' => 'm.fatih@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-15111996'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 3,
    //     'nama_karyawan' => 'MUSTAFA KEMAL PASHA',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 2,
    //     'no_hp' => '085740048206',
    //     'email' => 'mustafa.kemal@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-25071995'),
    //     'role' => 'spv',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 1,
    //     'nama_karyawan' => 'EDI SURATNO',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 3,
    //     'no_hp' => '089673677020',
    //     'email' => 'edi.suratno@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-06021975'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 1,
    //     'nama_karyawan' => 'LERIANA NURFALIZA',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 3,
    //     'no_hp' => '088239464547',
    //     'email' => 'leriana.nurfaliza@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-10061997'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 2,
    //     'nama_karyawan' => 'BAGUS BUDI SANTOSO',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 3,
    //     'no_hp' => '089669691286',
    //     'email' => 'bagus.budi@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-05082006'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 2,
    //     'nama_karyawan' => 'ILHAM DEVA PERMANA MALIK I.B',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 3,
    //     'no_hp' => '089616650507',
    //     'email' => 'ilham.deva@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-27012005'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 2,
    //     'nama_karyawan' => 'RIKI SAPUTRA AKBAR',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 3,
    //     'no_hp' => '089647294918',
    //     'email' => 'riki.saputra@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-10011999'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 3,
    //     'nama_karyawan' => 'NICOLAS SAPUTRA',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 3,
    //     'no_hp' => '089525614367',
    //     'email' => 'nicolas.saputra@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-28092003'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 2,
    //     'nama_karyawan' => 'RICKY DWI ANDIKA PUTRA',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 3,
    //     'no_hp' => '08995928523',
    //     'email' => 'ricky.dwi@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-25091993'),
    //     'role' => 'spv',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 5,
    //     'nama_karyawan' => 'HERDIKA ANGGIT SETIAWAN WIJAYANTO',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 4,
    //     'no_hp' => '081259155910',
    //     'email' => 'herdika.anggit@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-26082001'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 2,
    //     'nama_karyawan' => 'JOSUA BAKKARA',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 4,
    //     'no_hp' => '081325775316',
    //     'email' => 'josua.bakkara@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-28081999'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 2,
    //     'nama_karyawan' => 'RIZQI SAPUTRA',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 4,
    //     'no_hp' => '085559178526',
    //     'email' => 'rizqi.saputra@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-24012003'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 3,
    //     'nama_karyawan' => 'DAFA RAFAEL SATRIA WICAKSONO',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 4,
    //     'no_hp' => '085803623097',
    //     'email' => 'dafa.rafael@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-26052004'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 3,
    //     'nama_karyawan' => 'DAFFA EKA WARDANA',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 4,
    //     'no_hp' => '085700466073',
    //     'email' => 'daffa.eka@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-14032005'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 3,
    //     'nama_karyawan' => 'DIMAS ADITYA AMARTIYANA',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 4,
    //     'no_hp' => '088233492438',
    //     'email' => 'dimas.aditya@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-18062001'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 3,
    //     'nama_karyawan' => 'EDI SETIAWAN',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 4,
    //     'no_hp' => '088228933631',
    //     'email' => 'edi.setiawan@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-08062001'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 3,
    //     'nama_karyawan' => 'KOERUL IHSAN',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 4,
    //     'no_hp' => '082234422303',
    //     'email' => 'koerul.ihsan@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-07091995'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 3,
    //     'nama_karyawan' => 'M NUROCHIM',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 4,
    //     'no_hp' => '088238528011',
    //     'email' => 'm.nurochim@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-20112003'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 3,
    //     'nama_karyawan' => 'MUKHAMAD ZUNIHAR IRWANSYAH',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 4,
    //     'no_hp' => '08990488018',
    //     'email' => 'mukhamad.zunihar@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-02102001'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 3,
    //     'nama_karyawan' => 'YUSUF HIDAYAT',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 4,
    //     'no_hp' => '085339223514',
    //     'email' => 'yusuf.hidayat@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-19032000'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 2,
    //     'nama_karyawan' => 'NAUFAL LUTHFI WIJANARKO',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 4,
    //     'no_hp' => '089646393999',
    //     'email' => 'naufal.luthfi@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-12062000'),
    //     'role' => 'spv',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 3,
    //     'nama_karyawan' => 'IKA HADIYANI ARSILA',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 5,
    //     'no_hp' => '08980226072',
    //     'email' => 'ika.hadiyani@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-20091993'),
    //     'role' => 'spv',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 4,
    //     'nama_karyawan' => 'AMRULLOH MASUD',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 5,
    //     'no_hp' => '085869038094',
    //     'email' => 'amrulloh.masud@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-26062002'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 4,
    //     'nama_karyawan' => 'DANI ZAINU ILAH',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 5,
    //     'no_hp' => '0895330698544',
    //     'email' => 'dani.zainu@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-28092001'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 4,
    //     'nama_karyawan' => 'ZAIN AFIF HENDRIANTOKO',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 5,
    //     'no_hp' => '0811111111111',
    //     'email' => 'zain.afif@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-12345678'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 3,
    //     'nama_karyawan' => 'GIAN RADITYA',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 6,
    //     'no_hp' => '089524568490',
    //     'email' => 'gian.raditya@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-08061999'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 3,
    //     'nama_karyawan' => 'RIFAN RIYANTRIARNO RAMADANI',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 6,
    //     'no_hp' => '081327502378',
    //     'email' => 'rifan.riyantriarno@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-24011998'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 4,
    //     'nama_karyawan' => 'BAHRUM AROHMAN',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 6,
    //     'no_hp' => '085640459004',
    //     'email' => 'bahrum.arohman@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-96051984'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 2,
    //     'nama_karyawan' => 'SUPRIYONO',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 6,
    //     'no_hp' => '08122517656',
    //     'email' => 'supriyono@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-24081970'),
    //     'role' => 'spv',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 2,
    //     'nama_karyawan' => 'HARYANTO',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 7,
    //     'no_hp' => '081901092013',
    //     'email' => 'haryanto@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-10071972'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 2,
    //     'nama_karyawan' => 'MUHAMMAD BAGUS RIZAL WICAKSONO',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 7,
    //     'no_hp' => '089666026749',
    //     'email' => 'muhammad.bagus@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-29032004'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 2,
    //     'nama_karyawan' => 'SUNADI',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 7,
    //     'no_hp' => '081390150078',
    //     'email' => 'sunadi@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-12071971'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 2,
    //     'nama_karyawan' => 'THARIQ',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 7,
    //     'no_hp' => '089666729412',
    //     'email' => 'thariq@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-94111998'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 3,
    //     'nama_karyawan' => 'ROHADI',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 7,
    //     'no_hp' => '087700079532',
    //     'email' => 'rohadi@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-97041980'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 4,
    //     'nama_karyawan' => 'NITA PUTRI HAPSARI',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 7,
    //     'no_hp' => '082221917797',
    //     'email' => 'nita.putri@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-28101994'),
    //     'role' => 'spv',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 3,
    //     'nama_karyawan' => 'WINARNO',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 8,
    //     'no_hp' => '08814185663',
    //     'email' => 'winarno@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-25111978'),
    //     'role' => 'karyawan',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);

    // User::create([
    //     'toko_id' => 2,
    //     'nama_karyawan' => 'RIZKY SYAH GUMELAR',
    //     'default_shift_id' => 1,
    //     'divisi_id' => 9,
    //     'no_hp' => '085186844868',
    //     'email' => 'rizky.syah@hws.com',
    //     'email_verified_at' => now(),
    //     'password' => Hash::make('Pass-04062003'),
    //     'role' => 'spv',
    //     'total_cuti' => 24,
    //     'status' => 'aktif',
    // ]);
}
