@extends('layouts.manage')

@section('title', 'Manage Karyawan')

@section('page-title', 'Manage Karyawan')

@section('content')


<!-- Main content -->


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
                        <a href="{{ route('input-jadwal.export-all', $mingguKe) }}" class="btn btn-success my-4">Export Mingguan</a>
                        <a href="{{ route('mingguan.generateLaporanMingguanForAll', $mingguKe) }}" class="btn btn-success my-4">Refresh</a>
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <!-- <th>ID</th> -->
                                    <th>Nama Karyawan</th>
                                    <th>Mingguan</th>
                                    <th>Kehadiran</th>
                                    <th>Lembur</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                    <!-- <th>Sabtu</th>
                                        <th>Minggu</th>
                                        <th>Senin</th>
                                        <th>Selasa</th>
                                        <th>Rabu</th>
                                        <th>Kamis</th>
                                        <th>Jumat</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mingguans as $mingguan)
                                <tr data-widget="expandable-table" aria-expanded="false">
                                    <!-- <td>{{ $mingguan->id }}</td> -->
                                    <td>{{ $mingguan->users->nama_karyawan }}</td>
                                    <td>{{ $mingguan->uang_mingguan }}</td>
                                    <td>{{ $mingguan->uang_kedatangan }}</td>
                                    <td>{{ $mingguan->uang_lembur_mingguan  }}</td>
                                    <td>{{ $mingguan->uang_mingguan + $mingguan->uang_kedatangan + $mingguan->uang_lembur_mingguan }}</td>
                                    <td @if($mingguan->status == 'kurang') style="color: red;" @endif>{{ $mingguan->status }}</td>
                                    <td>

                                        <a href="{{ route('input-jadwal.export', ['user_id' => $mingguan->users->id, 'minggu_ke' => $mingguan->minggu_ke]) }}" class="btn btn-success">Export</a>
                                        <!-- <a href="{{ route('input-jadwal.export', ['user_id' => $mingguan->users->id, 'minggu_ke' => $mingguan->minggu_ke]) }}" class="btn btn-info">Detail</a> -->
                                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modalDetail{{ $mingguan->users->id }}">
                                            Detail
                                        </button>

                                    </td>
                                    <!-- <td>{{ $mingguan->d1 }}</td>
                                        <td>{{ $mingguan->d2 }}</td>
                                        <td>{{ $mingguan->d3 }}</td>
                                        <td>{{ $mingguan->d4 }}</td>
                                        <td>{{ $mingguan->d5 }}</td>
                                        <td>{{ $mingguan->d6 }}</td>
                                        <td>{{ $mingguan->d7 }}</td> -->
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

<!-- /.content -->

@foreach ($mingguans as $mingguan)
<!-- Modal Detail (harus ada di dalam foreach) -->

<div class="modal fade" id="modalDetail{{ $mingguan->users->id }}" tabindex="-1" aria-labelledby="modalDetailLabel{{ $mingguan->users->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalDetailLabel{{ $mingguan->users->id }}">Detail Kehadiran - {{ $mingguan->users->nama_karyawan }}</h1>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Sabtu</th>
                            <th>Minggu</th>
                            <th>Senin</th>
                            <th>Selasa</th>
                            <th>Rabu</th>
                            <th>Kamis</th>
                            <th>Jumat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>Shift</th>
                            <x-shift-cell :data="$mingguan->d1" />
                            <x-shift-cell :data="$mingguan->d2" />
                            <x-shift-cell :data="$mingguan->d3" />
                            <x-shift-cell :data="$mingguan->d4" />
                            <x-shift-cell :data="$mingguan->d5" />
                            <x-shift-cell :data="$mingguan->d6" />
                            <x-shift-cell :data="$mingguan->d7" />
                        </tr>
                        <tr>
                            <th>Jam Masuk</th>
                            <td>{{ json_decode($mingguan->d1, true)['jam_masuk'] ?? 'null' }}</td>
                            <td>{{ json_decode($mingguan->d2, true)['jam_masuk'] ?? 'null' }}</td>
                            <td>{{ json_decode($mingguan->d3, true)['jam_masuk'] ?? 'null' }}</td>
                            <td>{{ json_decode($mingguan->d4, true)['jam_masuk'] ?? 'null' }}</td>
                            <td>{{ json_decode($mingguan->d5, true)['jam_masuk'] ?? 'null' }}</td>
                            <td>{{ json_decode($mingguan->d6, true)['jam_masuk'] ?? 'null' }}</td>
                            <td>{{ json_decode($mingguan->d7, true)['jam_masuk'] ?? 'null' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection