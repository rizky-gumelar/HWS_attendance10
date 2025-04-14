@extends('layouts.manage')

@section('title', 'Manage')

@section('page-title', 'Manage')

@section('content')

<!-- general form elements -->
<div class="card card-primary m-4">
    <div class="card-header">
        <h3 class="card-title">Tambah Tipe Lembur</h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
    <form action="{{ route('lembur.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label for="tipe_lembur">Tipe Lembur</label>
                <input type="text" class="form-control" id="tipe_lembur" name="tipe_lembur" placeholder="Masukkan nama" required>
            </div>
            <div class="form-group">
                <label for="biaya">Biaya</label>
                <input type="numeric" class="form-control" id="biaya" name="biaya" required>
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

@endsection