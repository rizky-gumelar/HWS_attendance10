@php
$role = auth()->user()->role;

if (in_array($role, ['admin', 'super_admin'])) {
$layout = 'layouts.manage';
} elseif (in_array($role, ['spv', 'supervisor'])) {
$layout = 'layouts.spv_manage';
} else {
$layout = 'layouts.karyawan_manage';
}
@endphp

@extends($layout)

@section('title', 'Jadwal Karyawan')

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
                                <input type="file" name="csv_file" accept=".csv,.xlsx" required>
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
                                <div class="col-md-4 d-flex align-items-end mt-3">
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
                        <!-- <a href="{{ route('generate.jadwal.bulanan') }}" class="btn btn-danger my-4">Generate Jadwal</a> -->
                        <!-- Tombol Generate Tanpa Menimpa -->
                        @if(auth()->user()->role === 'admin')
                        <div class="d-flex gap-2">
                            <form method="POST" action="{{ route('generate.jadwal.admin') }}" id="form-tanpa-timpa">
                                @csrf
                                <input type="hidden" name="overwrite" value="0">
                                <button type="submit" class="btn btn-primary">
                                    Generate Jadwal Bulanan
                                </button>
                            </form>

                            <form method="POST" action="{{ route('generate.jadwal') }}">
                                @csrf
                                <button type="submit" class="btn btn-warning">
                                    Generate Jadwal Mingguan
                                </button>
                            </form>
                        </div>
                        @else
                        <form method="POST" action="{{ route('generate.jadwal.bulanan') }}" id="form-tanpa-timpa">
                            @csrf
                            <input type="hidden" name="overwrite" value="0">
                            <button type="submit" class="btn btn-primary">
                                Generate Jadwal Bulanan
                            </button>
                        </form>

                        <!-- Tombol Generate & Timpa, pakai konfirmasi -->
                        <form method="POST" action="{{ route('generate.jadwal.bulanan') }}" id="form-timpa-jadwal">
                            @csrf
                            <input type="hidden" name="overwrite" value="1">
                            <button type="button" class="btn btn-warning" onclick="konfirmasiTimpa()">
                                Generate & Overwite Jadwal Bulanan
                            </button>
                        </form>
                        <!-- <a href="{{ route('input-jadwal.import') }}" class="btn btn-primary my-4">Import CSV</a> -->
                        @endif

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
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($input_jadwals as $input_jadwal)
                                <tr>
                                    <!-- <td>{{ $input_jadwal->id }}</td> -->
                                    <td>{{ \Carbon\Carbon::parse($input_jadwal->tanggal)->locale('id')->isoFormat('YYYY-MM-DD, dddd ') }}</td>
                                    <td>{{ $input_jadwal->users->nama_karyawan }}</td>
                                    <td>{{ $input_jadwal->shift->nama_shift }}</td>
                                    <td>{{ $input_jadwal->absensi->jam_masuk ?? '-' }}</td>
                                    <td>
                                        @if($input_jadwal->lembur && $input_jadwal->lembur_jam)
                                        {{ $input_jadwal->lembur->tipe_lembur . ' - ' . $input_jadwal->lembur_jam . ' jam' }}
                                        @else
                                        -
                                        @endif
                                    </td>

                                    <td>{{ $input_jadwal->keterlambatan_name }}</td>
                                    <td>Minggu ke-{{ $input_jadwal->minggu_ke }}</td>
                                    <td>
                                        <a href="{{ route('input-jadwal.edit', $input_jadwal->id) }}" class="btn btn-warning" target="_blank">Edit</a>
                                        <form action="{{ route('input-jadwal.destroy', $input_jadwal->id) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger" onclick="return confirm('Apakah anda yakin?')">Delete</button>
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