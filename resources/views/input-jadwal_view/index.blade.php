@php
$layout = auth()->user()->role === 'admin' ? 'layouts.manage' : 'layouts.spv_manage';
@endphp

@extends($layout)

@section('title', 'Manage')

@section('page-title', 'Manage')

@section('content')
<!-- Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="importModalLabel">Upload File CSV</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Download template excel :
                    <a href="{{ route('export-template') }}">Template Excel</a>
                </p>
                <form action="{{ route('input-jadwal.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="exampleInputFile">File input</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="csv_file" accept=".csv" required>
                                <button type="submit" class="input-group-text">Impor Jadwal</button>
                            </div>
                            <div class="input-group-append">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Input Jadwal Karyawan</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Input Jadwal Karyawan</li>
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
                        <form action="{{ route('input-jadwal.index') }}" method="GET">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="start_date">Dari Tanggal:</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="end_date">Sampai Tanggal:</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary">Cari</button>
                                    <a href="{{ route('input-jadwal.index') }}" class="btn btn-secondary ml-2">Reset</a>
                                </div>
                            </div>
                        </form>
                        <a href="{{ route('input-jadwal.create') }}" class="btn btn-success my-4">Input Jadwal</a>
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#importModal">
                            Import Jadwal
                        </button>
                        <a href="{{ route('generate.jadwal') }}" class="btn btn-danger my-4">Generate Jadwal</a>
                        <!-- <a href="{{ route('input-jadwal.import') }}" class="btn btn-primary my-4">Import CSV</a> -->

                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Karyawan</th>
                                    <th>Tanggal</th>
                                    <th>Shift</th>
                                    <th>Jam Masuk</th>
                                    <th>Lembur</th>
                                    <th>Keterangan</th>
                                    <th>Minggu ke</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($input_jadwals as $input_jadwal)
                                <tr>
                                    <td>{{ $input_jadwal->id }}</td>
                                    <td>{{ $input_jadwal->users->nama_karyawan }}</td>
                                    <td>{{ \Carbon\Carbon::parse($input_jadwal->tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</td>
                                    <td>{{ $input_jadwal->shift->nama_shift }}</td>
                                    <td>{{ $input_jadwal->absensi->jam_masuk ?? '-' }}</td>
                                    <td>{{ $input_jadwal->lembur->tipe_lembur ?? '-' }}</td>
                                    <td>{{ $input_jadwal->keterlambatan_name }}</td>
                                    <td>Minggu ke-{{ $input_jadwal->minggu_ke }}</td>
                                    <td>
                                        <a href="{{ route('input-jadwal.edit', $input_jadwal->id) }}" class="btn btn-warning">Edit</a>
                                        <form action="{{ route('input-jadwal.destroy', $input_jadwal->id) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
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

<!-- /.content -->
@endsection