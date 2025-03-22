@extends('layouts.manage')

@section('title', 'Manage')

@section('page-title', 'Manage')

@section('content')
<!-- Main content -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
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
                            <a href="{{ route('input-jadwal.create') }}" class="btn btn-success my-4">Input Jadwal</a>
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama Karyawan</th>
                                        <th>Shift</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($input_jadwals as $input_jadwal)
                                    <tr>
                                        <td>{{ $input_jadwal->id }}</td>
                                        <td>{{ $input_jadwal->users->nama_karyawan }}</td>
                                        <td>{{ $input_jadwal->shift->nama_shift }}</td>
                                        <td>{{ $input_jadwal->tanggal }}</td>
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
</div>
<!-- /.content -->
@endsection