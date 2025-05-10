<!-- <h1>Karyawan Dashboard</h1>
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Logout</button>
</form> -->


<!-- $saldo = auth()->user()->total_cuti;
$poin = auth()->user()->poin_tidak_hadir; -->
@php
$percentCuti = ($karyawan->total_cuti != 0) ? $karyawan->sisa_cuti / $karyawan->total_cuti * 100 : 0;
$percentPoin = ($karyawan->poin_tidak_hadir != 0) ? $karyawan->poin_terakhir / $karyawan->poin_tidak_hadir * 100 : 0;
@endphp
@extends('layouts.karyawan_manage')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section('content')<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Dashboard</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>
<section class="content">
    <div class="container-fluid">
        <!-- Menambahkan penyesuaian kolom untuk layar besar -->
        <div class="row">
            <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                <div class="small-box bg-info">
                    <div class="info-box bg-info shadow-none">
                        <span class="info-box-icon"><i class="fas fa-outdent"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Sisa Saldo Cuti</span>
                            <span class="info-box-number">{{ $karyawan->sisa_cuti }} / {{$karyawan->total_cuti}}</span>

                            <div class="progress">
                                <div class="progress-bar" style="width: {{ $percentCuti }}%"></div>
                            </div>
                        </div>
                    </div>
                    <!-- <a href="#" class="small-box-footer">
                        More info <i class="fas fa-arrow-circle-right"></i>
                    </a> -->
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                <div class="small-box bg-success">
                    <div class="info-box bg-success shadow-none">
                        <span class="info-box-icon"><i class="fas fa-outdent"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Sisa Poin Ketidakhadiran</span>
                            <span class="info-box-number">{{ $karyawan->poin_terakhir }} / {{$karyawan->poin_tidak_hadir}} </span>

                            <div class="progress">
                                <div class="progress-bar" style="width: {{ $percentPoin }}%"></div>
                            </div>
                        </div>
                    </div>
                    <!-- <a href="#" class="small-box-footer">
                        More info <i class="fas fa-arrow-circle-right"></i>
                    </a> -->
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h3>Riwayat Keterlambatan</h3>
                <table id="example3" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <!-- <th>ID</th> -->
                            <th>Tanggal</th>
                            <th>Nama Karyawan</th>
                            <th>Shift</th>
                            <th>Jam Masuk</th>
                            <th>Lembur</th>
                            <th>Keterangan</th>
                            <th>Minggu ke</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($telats as $telat)
                        <tr>
                            <!-- <td>{{ $telat->id }}</td> -->
                            <td>{{ \Carbon\Carbon::parse($telat->tanggal)->locale('id')->isoFormat('YYYY-MM-DD, dddd ') }}</td>
                            <td>{{ $telat->users->nama_karyawan }}</td>
                            <td>{{ $telat->shift->nama_shift }}</td>
                            <td>{{ $telat->absensi->jam_masuk ?? '-' }}</td>
                            <td>
                                @if($telat->lembur && $telat->lembur_jam)
                                {{ $telat->lembur->tipe_lembur . ' - ' . $telat->lembur_jam . ' jam' }}
                                @else
                                -
                                @endif
                            </td>

                            <td>{{ $telat->keterlambatan_name }}</td>
                            <td>Minggu ke-{{ $telat->minggu_ke }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>


@endsection