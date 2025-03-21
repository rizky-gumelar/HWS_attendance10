@extends('layouts.manage')

@section('title', 'Manage')

@section('page-title', 'Manage')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- general form elements -->
    <div class="card card-primary m-4">
        <div class="card-header">
            <h3 class="card-title">Tambah Jenis Cuti</h3>
        </div>
        <!-- /.card-header -->
        <!-- form start -->
        <form action="{{ route('jenis-cuti.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="nama_cuti">Nama Cuti</label>
                    <input type="text" class="form-control" id="nama_cuti" name="nama_cuti" placeholder="Masukkan nama" required>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <input type="number" class="form-control" id="status" name="status" required>
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