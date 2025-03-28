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
                            <form action="{{ route('mingguan.index') }}" method="GET">
                                <label for="minggu_ke">Pilih Minggu Ke:</label>
                                <select name="minggu_ke" id="minggu_ke" class="form-control">
                                    @for ($i = 1; $i <= 52; $i++)
                                        <option value="{{ $i }}" {{ $i == $mingguKe ? 'selected' : '' }}>Minggu ke-{{ $i }}</option>
                                        @endfor
                                </select>
                                <button type="submit" class="btn btn-primary mt-2">Tampilkan</button>
                            </form>
                            <!-- Menampilkan rentang tanggal dari minggu yang dipilih -->
                            @if(isset($startDate) && isset($endDate))
                            <p class="mt-3"><strong>Periode Tanggal:</strong> {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}</p>
                            @endif
                            <a href="#" class="btn btn-success my-4">Export Mingguan</a>
                            <a href="{{ route('mingguan.generateLaporanMingguanForAll', $mingguKe) }}" class="btn btn-success my-4">Refresh</a>
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <!-- <th>ID</th> -->
                                        <th>Nama Karyawan</th>
                                        <th>Sabtu</th>
                                        <th>Minggu</th>
                                        <th>Senin</th>
                                        <th>Selasa</th>
                                        <th>Rabu</th>
                                        <th>Kamis</th>
                                        <th>Jumat</th>
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
                                        <td>{{ $mingguan->d1 }}</td>
                                        <td>{{ $mingguan->d2 }}</td>
                                        <td>{{ $mingguan->d3 }}</td>
                                        <td>{{ $mingguan->d4 }}</td>
                                        <td>{{ $mingguan->d5 }}</td>
                                        <td>{{ $mingguan->d6 }}</td>
                                        <td>{{ $mingguan->d7 }}</td>
                                        <td>{{ $mingguan->uang_mingguan }}</td>
                                        <td>{{ $mingguan->uang_kedatangan }}</td>
                                        <td>{{ $mingguan->uang_lembur_mingguan  }}</td>
                                        <td>{{ $mingguan->uang_mingguan + $mingguan->uang_kedatangan + $mingguan->uang_lembur_mingguan }}</td>
                                        <td>

                                            <a href="{{ route('input-jadwal.export', ['user_id' => $mingguan->users->id, 'minggu_ke' => $mingguan->minggu_ke]) }}" class="btn btn-success">Export</a>
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