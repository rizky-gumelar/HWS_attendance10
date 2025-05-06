@extends('layouts.manage')

@section('title', 'Manage')

@section('page-title', 'Manage')

@section('content')
<!-- Main content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Pengaturan</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Keuangan</li>
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

                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Keuangan</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Uang Mingguan</th>
                                    <th>Uang Kedatangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $keuangan->uang_mingguan }}</td>
                                    <td>{{ $keuangan->uang_kedatangan }}</td>
                                    <td>
                                        <a href="{{ route('keuangan.edit', $keuangan->id) }}" class="btn btn-warning">Edit</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->

                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Toleransi Keterlambatan</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <form action="{{ route('pengaturan.update') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label for="toleransi_masuk">Toleransi Masuk (menit)</label>
                                <input type="number" name="toleransi_masuk" class="form-control"
                                    value="{{ old('toleransi_masuk', $toleransi) }}">
                                @error('toleransi_masuk')
                                <small class=" text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary mt-2">Simpan</button>
                        </form>
                    </div>
                    <!-- /.card-body -->
                </div>

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