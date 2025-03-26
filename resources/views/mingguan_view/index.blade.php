@extends('layouts.manage')

@section('title', 'Manage Karyawan')

@section('page-title', 'Manage Karyawan')

@section('content')
<!-- Main content -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Laporan Mingguan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Laporan Mingguan</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="card">
                        <!-- /.card-header -->
                        <div class="card-body">
                            <a href="#" class="btn btn-success my-4">Export Mingguan</a>
                            <a href="{{ route('mingguan.generateLaporanMingguanForAll', 13) }}" class="btn btn-success my-4">Refresh</a>
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <!-- <th>ID</th> -->
                                        <th>Nama Karyawan</th>
                                        <th>S</th>
                                        <th>M</th>
                                        <th>S</th>
                                        <th>S</th>
                                        <th>R</th>
                                        <th>K</th>
                                        <th>J</th>
                                        <th>Mingguan</th>
                                        <th>Kehadiran</th>
                                        <th>Lembur</th>
                                        <th>Total</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($mingguans as $mingguan)
                                    <tr>
                                        <!-- <td>{{ $mingguan->id }}</td> -->
                                        <td>{{ $mingguan->users->nama_karyawan }}</td>
                                        <td>{{ $mingguan->d1 ?? '-' }}</td>
                                        <td>{{ $mingguan->d2 ?? '-' }}</td>
                                        <td>{{ $mingguan->d3 ?? '-' }}</td>
                                        <td>{{ $mingguan->d4 ?? '-' }}</td>
                                        <td>{{ $mingguan->d5 ?? '-' }}</td>
                                        <td>{{ $mingguan->d6 ?? '-' }}</td>
                                        <td>{{ $mingguan->d7 ?? '-' }}</td>
                                        <td>{{ $mingguan->uang_mingguan }}</td>
                                        <td>{{ $mingguan->uang_kedatangan }}</td>
                                        <td>{{ $mingguan->uang_lembur_mingguan  }}</td>
                                        <td>{{ $mingguan->uang_mingguan + $mingguan->uang_kedatangan + $mingguan->uang_lembur_mingguan }}</td>
                                        <td>

                                            <a href="{{ route('input-jadwal.export', $mingguan->users->id) }}" class="btn btn-success">Export</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content -->
@endsection