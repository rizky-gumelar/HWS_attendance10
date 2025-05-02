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

@section('title', 'Manage')

@section('page-title', 'Manage')

@section('content')
<!-- Main content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Jadwal Karyawan</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Jadwal Karyawan</li>
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
                        <form action="{{ route('input-jadwal.read') }}" method="GET">
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

                        <table id="example3" class="table table-bordered table-striped">
                            <thead>
                                <tr>
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
                                @foreach ($input_jadwals as $input_jadwal)
                                <tr>
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