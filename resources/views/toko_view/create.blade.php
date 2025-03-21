@extends('layouts.manage')

@section('title', 'Manage')

@section('page-title', 'Manage')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- general form elements -->
    <div class="card card-primary m-4">
        <div class="card-header">
            <h3 class="card-title">Tambah Toko</h3>
        </div>
        <!-- /.card-header -->
        <!-- form start -->
        <form action="{{ route('toko.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="nama_toko">Nama Toko</label>
                    <input type="text" class="form-control" id="nama_toko" name="nama_toko" placeholder="Masukkan nama" required>
                </div>
                <div class="form-group">
                    <label for="alamat">Alamat</label>
                    <input type="text" class="form-control" id="alamat" name="alamat" required>
                </div>
            </div>
            <!-- /.card-body -->

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
                <!-- Tombol Batal -->
                <a href="{{ url()->previous() }}" class="btn btn-danger">Batal</a>
            </div>
        </form>
    </div>
    <!-- /.card -->
</div>
@endsection