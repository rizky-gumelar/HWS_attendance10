@extends('layouts.manage')

@section('title', 'Manage')

@section('page-title', 'Manage')

@section('content')

<!-- general form elements -->
<div class="card card-primary m-4">
    <div class="card-header">
        <h3 class="card-title">Edit Uang Mingguan</h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
    <form action="{{ route('keuangan.update', $keuangan->id) }}" method="POST">
        @csrf
        @method('PUT') <!-- Laravel membutuhkan method PUT untuk update -->

        <div class="card-body">
            <div class="form-group">
                <label for="uang_mingguan">Uang Mingguan</label>
                <input type="number" class="form-control" id="uang_mingguan" name="uang_mingguan" placeholder="Masukkan uang mingguan" value="{{ old('uang_mingguan', $keuangan->uang_mingguan) }}" required>
            </div>
            <div class="form-group">
                <label for="uang_kedatangan">Uang Kedatangan</label>
                <input type="number" class="form-control" id="uang_kedatangan" name="uang_kedatangan" placeholder="Masukkan uang mingguan" value="{{ old('uang_kedatangan', $keuangan->uang_kedatangan) }}" required>
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