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
                            <a href="{{ route('manage-karyawan.create') }}" class="btn btn-success my-4">Tambah Karyawan</a>
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama Karyawan</th>
                                        <th>Store</th>
                                        <th>Divisi</th>
                                        <th>Email</th>
                                        <th>No Telepon</th>
                                        <th>Role</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($karyawans as $karyawan)
                                    <tr>
                                        <td>{{ $karyawan->id }}</td>
                                        <td>{{ $karyawan->nama_karyawan }}</td>
                                        <td>{{ $karyawan->toko->nama_toko ?? 'Toko Tidak Ditemukan' }}</td>
                                        <td>{{ $karyawan->divisi }}</td>
                                        <td>{{ $karyawan->email }}</td>
                                        <td>{{ $karyawan->no_hp }}</td>
                                        <td>{{ $karyawan->role_name  }}</td>
                                        <td>
                                            <a href="{{ route('manage-karyawan.edit', $karyawan->id) }}" class="btn btn-warning">Edit</a>
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
</div>
<!-- /.content -->
@endsection