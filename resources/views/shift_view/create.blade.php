@php
$layout = auth()->user()->role === 'admin' ? 'layouts.manage' : 'layouts.spv_manage';
@endphp

@extends($layout)

@section('title', 'Manage')

@section('page-title', 'Manage')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- general form elements -->
    <div class="card card-primary m-4">
        <div class="card-header">
            <h3 class="card-title">Tambah Shift</h3>
        </div>
        <!-- /.card-header -->
        <!-- form start -->
        <form action="{{ route('shift.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="nama_shift">Nama Shift</label>
                    <input type="text" class="form-control" id="nama_shift" name="nama_shift" placeholder="Masukkan nama" required>
                </div>
                <div class="form-group">
                    <label for="shift_masuk">Jam Masuk</label>
                    <input type="time" class="form-control" id="shift_masuk" name="shift_masuk" required>
                </div>
                <div class="form-group">
                    <label for="shift_keluar">Jam Keluar</label>
                    <input type="time" class="form-control" id="shift_keluar" name="shift_keluar" required>
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