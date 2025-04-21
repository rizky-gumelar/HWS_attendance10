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
            'toko_id' => 1,
            'nama_karyawan' => 'Admin User',
            'default_shift_id' => 1,
            'divisi_id' => 2,
            'no_hp' => '081234567890',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'admin',
            'total_cuti' => 0,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 3,
            'nama_karyawan' => 'DAFFA ARYA SENA HEDEN',
            'default_shift_id' => 1,
            'divisi_id' => 1,
            'no_hp' => '81390040402',
            'email' => 'daffa.arya@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('6072004'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 2,
            'nama_karyawan' => 'TYAH DIAN OCHTAVIYANA',
            'default_shift_id' => 1,
            'divisi_id' => 1,
            'no_hp' => '8886699074',
            'email' => 'tyah.dian@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('29101993'),
            'role' => 'spv',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 3,
            'nama_karyawan' => 'NAWANGGI DWINDA ARSILA',
            'default_shift_id' => 1,
            'divisi_id' => 2,
            'no_hp' => '895360526882',
            'email' => 'nawanggi.dwinda@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12072000'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 3,
            'nama_karyawan' => 'TONI IQBAL RAMDHANI',
            'default_shift_id' => 1,
            'divisi_id' => 2,
            'no_hp' => '85155090454',
            'email' => 'toni.iqbal@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('24081995'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 4,
            'nama_karyawan' => 'M FATIH KHOIRUL IBAD',
            'default_shift_id' => 1,
            'divisi_id' => 2,
            'no_hp' => '82244536790',
            'email' => 'm.fatih@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('15111996'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 3,
            'nama_karyawan' => 'MUSTAFA KEMAL PASHA',
            'default_shift_id' => 1,
            'divisi_id' => 2,
            'no_hp' => '85740048206',
            'email' => 'mustafa.kemal@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('25071995'),
            'role' => 'spv',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 1,
            'nama_karyawan' => 'EDI SURATNO',
            'default_shift_id' => 1,
            'divisi_id' => 3,
            'no_hp' => '89673677020',
            'email' => 'edi suratno@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('6021975'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 1,
            'nama_karyawan' => 'LERIANA NURFALIZA',
            'default_shift_id' => 1,
            'divisi_id' => 3,
            'no_hp' => '88239464547',
            'email' => 'leriana nurfaliza@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('10061997'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 2,
            'nama_karyawan' => 'BAGUS BUDI SANTOSO',
            'default_shift_id' => 1,
            'divisi_id' => 3,
            'no_hp' => '89669691286',
            'email' => 'bagus.budi@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('5082006'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 2,
            'nama_karyawan' => 'ILHAM DEVA PERMANA MALIK I.B',
            'default_shift_id' => 1,
            'divisi_id' => 3,
            'no_hp' => '89616650507',
            'email' => 'ilham.deva@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('27012005'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 2,
            'nama_karyawan' => 'RIKI SAPUTRA AKBAR',
            'default_shift_id' => 1,
            'divisi_id' => 3,
            'no_hp' => '89647294918',
            'email' => 'riki.saputra@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('10011999'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 3,
            'nama_karyawan' => 'NICOLAS SAPUTRA',
            'default_shift_id' => 1,
            'divisi_id' => 3,
            'no_hp' => '89525614367',
            'email' => 'nicolas saputra@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('28092003'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 2,
            'nama_karyawan' => 'RICKY DWI ANDIKA PUTRA',
            'default_shift_id' => 1,
            'divisi_id' => 3,
            'no_hp' => '8995928523',
            'email' => 'ricky.dwi@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('25091993'),
            'role' => 'spv',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 5,
            'nama_karyawan' => 'HERDIKA ANGGIT SETIAWAN WIJAYANTO',
            'default_shift_id' => 1,
            'divisi_id' => 4,
            'no_hp' => '81259155910',
            'email' => 'herdika.anggit@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('26082001'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 2,
            'nama_karyawan' => 'JOSUA BAKKARA',
            'default_shift_id' => 1,
            'divisi_id' => 4,
            'no_hp' => '81325775316',
            'email' => 'josua bakkara@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('28081999'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 2,
            'nama_karyawan' => 'RIZQI SAPUTRA',
            'default_shift_id' => 1,
            'divisi_id' => 4,
            'no_hp' => '85559178526',
            'email' => 'rizqi saputra@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('24012003'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 3,
            'nama_karyawan' => 'DAFA RAFAEL SATRIA WICAKSONO',
            'default_shift_id' => 1,
            'divisi_id' => 4,
            'no_hp' => '85803623097',
            'email' => 'dafa.rafael@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('26052004'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 3,
            'nama_karyawan' => 'DAFFA EKA WARDANA',
            'default_shift_id' => 1,
            'divisi_id' => 4,
            'no_hp' => '85700466073',
            'email' => 'daffa.eka@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('14032005'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 3,
            'nama_karyawan' => 'DIMAS ADITYA AMARTIYANA',
            'default_shift_id' => 1,
            'divisi_id' => 4,
            'no_hp' => '88233492438',
            'email' => 'dimas.aditya@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('18062001'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 3,
            'nama_karyawan' => 'EDI SETIAWAN',
            'default_shift_id' => 1,
            'divisi_id' => 4,
            'no_hp' => '88228933631',
            'email' => 'edi setiawan@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('8062001'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 3,
            'nama_karyawan' => 'KOERUL IHSAN',
            'default_shift_id' => 1,
            'divisi_id' => 4,
            'no_hp' => '82234422303',
            'email' => 'koerul ihsan@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('7091995'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 3,
            'nama_karyawan' => 'M NUROCHIM',
            'default_shift_id' => 1,
            'divisi_id' => 4,
            'no_hp' => '88238528011',
            'email' => 'm nurochim@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('20112003'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 3,
            'nama_karyawan' => 'MUKHAMAD ZUNIHAR IRWANSYAH',
            'default_shift_id' => 1,
            'divisi_id' => 4,
            'no_hp' => '8990488018',
            'email' => 'mukhamad.zunihar@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('2102001'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 3,
            'nama_karyawan' => 'YUSUF HIDAYAT',
            'default_shift_id' => 1,
            'divisi_id' => 4,
            'no_hp' => '85339223514',
            'email' => 'yusuf hidayat@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('19032000'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 2,
            'nama_karyawan' => 'NAUFAL LUTHFI WIJANARKO',
            'default_shift_id' => 1,
            'divisi_id' => 4,
            'no_hp' => '89646393999',
            'email' => 'naufal.luthfi@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12062000'),
            'role' => 'spv',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 3,
            'nama_karyawan' => 'IKA HADIYANI ARSILA',
            'default_shift_id' => 1,
            'divisi_id' => 5,
            'no_hp' => '8980226072',
            'email' => 'ika.hadiyani@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('20091993'),
            'role' => 'spv',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 4,
            'nama_karyawan' => 'AMRULLOH MASUD',
            'default_shift_id' => 1,
            'divisi_id' => 5,
            'no_hp' => '85869038094',
            'email' => 'amrulloh masud@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('26062002'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 4,
            'nama_karyawan' => 'DANI ZAINU ILAH',
            'default_shift_id' => 1,
            'divisi_id' => 5,
            'no_hp' => '895330698544',
            'email' => 'dani.zainu@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('28092001'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 4,
            'nama_karyawan' => 'ZAIN AFIF HENDRIANTOKO',
            'default_shift_id' => 1,
            'divisi_id' => 5,
            'no_hp' => '811111111111',
            'email' => 'zain.afif@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 3,
            'nama_karyawan' => 'GIAN RADITYA',
            'default_shift_id' => 1,
            'divisi_id' => 6,
            'no_hp' => '89524568490',
            'email' => 'gian raditya@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('8061999'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 3,
            'nama_karyawan' => 'RIFAN RIYANTRIARNO RAMADANI',
            'default_shift_id' => 1,
            'divisi_id' => 6,
            'no_hp' => '81327502378',
            'email' => 'rifan.riyantriarno@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('24011998'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 4,
            'nama_karyawan' => 'BAHRUM AROHMAN',
            'default_shift_id' => 1,
            'divisi_id' => 6,
            'no_hp' => '85640459004',
            'email' => 'bahrum arohman@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('96051984'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 2,
            'nama_karyawan' => 'SUPRIYONO',
            'default_shift_id' => 1,
            'divisi_id' => 6,
            'no_hp' => '8122517656',
            'email' => 'supriyono@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('24081970'),
            'role' => 'spv',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 2,
            'nama_karyawan' => 'HARYANTO',
            'default_shift_id' => 1,
            'divisi_id' => 7,
            'no_hp' => '81901092013',
            'email' => 'haryanto@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('10071972'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 2,
            'nama_karyawan' => 'MUHAMMAD BAGUS RIZAL WICAKSONO',
            'default_shift_id' => 1,
            'divisi_id' => 7,
            'no_hp' => '89666026749',
            'email' => 'muhammad.bagus@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('29032004'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 2,
            'nama_karyawan' => 'SUNADI',
            'default_shift_id' => 1,
            'divisi_id' => 7,
            'no_hp' => '81390150078',
            'email' => 'sunadi@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12071971'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 2,
            'nama_karyawan' => 'THARIQ',
            'default_shift_id' => 1,
            'divisi_id' => 7,
            'no_hp' => '89666729412',
            'email' => 'thariq@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('94111998'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 3,
            'nama_karyawan' => 'ROHADI',
            'default_shift_id' => 1,
            'divisi_id' => 7,
            'no_hp' => '87700079532',
            'email' => 'rohadi@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('97041980'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 4,
            'nama_karyawan' => 'NITA PUTRI HAPSARI',
            'default_shift_id' => 1,
            'divisi_id' => 7,
            'no_hp' => '82221917797',
            'email' => 'nita.putri@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('28101994'),
            'role' => 'spv',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 3,
            'nama_karyawan' => 'WINARNO',
            'default_shift_id' => 1,
            'divisi_id' => 8,
            'no_hp' => '8814185663',
            'email' => 'winarno@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('25111978'),
            'role' => 'karyawan',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);

        User::create([
            'toko_id' => 2,
            'nama_karyawan' => 'RIZKY SYAH GUMELAR',
            'default_shift_id' => 1,
            'divisi_id' => 9,
            'no_hp' => '85186844868',
            'email' => 'rizky.syah@hws.com',
            'email_verified_at' => now(),
            'password' => Hash::make('4062003'),
            'role' => 'spv',
            'total_cuti' => 24,
            'status' => 'aktif',
        ]);
    }
}
