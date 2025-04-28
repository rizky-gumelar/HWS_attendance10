@php
$layout = auth()->user()->role === 'admin' ? 'layouts.manage' : 'layouts.spv_manage';
@endphp

@extends($layout)

@section('title', 'Manage Karyawan')

@section('page-title', 'Manage Karyawan')

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
                    <a href="{{ route('export-template-karyawan') }}">Template Excel</a>
                </p>
                <form action="{{ route('manage-karyawan.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="exampleInputFile">File input</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="csv_file" accept=".csv" required>
                                <button type="submit" class="input-group-text">Impor Karyawan</button>
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
                <h1>Manage Karyawan</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Manage Karyawan</li>
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
                        @if(auth()->user()->role === 'admin')
                        <a href="{{ route('manage-karyawan.create') }}" class="btn btn-success my-4">Tambah Karyawan</a>
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#importModal">
                            Import Karyawan
                        </button>
                        @endif
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Karyawan</th>
                                    <th>Store</th>
                                    <th>Shift</th>
                                    <th>Divisi</th>
                                    <th>Email</th>
                                    <th>No Telepon</th>
                                    <th>Role</th>
                                    <th>Total Cuti</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($karyawans as $karyawan)
                                <tr>
                                    <td @if($karyawan->status == 'nonaktif') style="color: red;" @endif>{{ $karyawan->id }}</td>
                                    <td @if($karyawan->status == 'nonaktif') style="color: red;" @endif>{{ $karyawan->nama_karyawan }}</td>
                                    <td @if($karyawan->status == 'nonaktif') style="color: red;" @endif>{{ $karyawan->toko->nama_toko ?? 'Toko Tidak Ditemukan' }}</td>
                                    <td @if($karyawan->status == 'nonaktif') style="color: red;" @endif>{{ $karyawan->shift->nama_shift ?? 'Shift Tidak Ditemukan' }}</td>
                                    <td @if($karyawan->status == 'nonaktif') style="color: red;" @endif>{{ $karyawan->divisi->nama_divisi }}</td>
                                    <td @if($karyawan->status == 'nonaktif') style="color: red;" @endif>{{ $karyawan->email }}</td>
                                    <td @if($karyawan->status == 'nonaktif') style="color: red;" @endif>{{ $karyawan->no_hp }}</td>
                                    <td @if($karyawan->status == 'nonaktif') style="color: red;" @endif>{{ $karyawan->role_name  }}</td>
                                    <td @if($karyawan->status == 'nonaktif') style="color: red;" @endif>{{ $karyawan->total_cuti  }}</td>
                                    <td @if($karyawan->status == 'nonaktif') style="color: red;" @endif>{{ $karyawan->status  }}</td>
                                    <td>
                                        <a href="{{ route('manage-karyawan.edit', $karyawan->id) }}" class="btn btn-warning">Edit</a>
                                        <!-- <a href="{{ route('input-jadwal.export', $karyawan->id) }}" class="btn btn-warning">Export</a> -->
                                        <form action="{{ route('manage-karyawan.destroy', $karyawan->id) }}" method="POST" class="d-inline">
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