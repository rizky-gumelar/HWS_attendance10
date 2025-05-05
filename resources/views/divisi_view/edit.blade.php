@extends('layouts.manage')

@section('title', 'Edit Divisi')

@section('page-title', 'Edit Divisi')

@section('content')

<!-- general form elements -->
<div class="card card-primary m-4">
    <div class="card-header">
        <h3 class="card-title">Edit Divisi</h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
    <form action="{{ route('divisi.update', $divisi->id) }}" method="POST">
        @csrf
        @method('PUT') <!-- Laravel membutuhkan method PUT untuk update -->

        <div class="card-body">
            <div class="form-group">
                <label for="nama_divisi">Nama Divisi</label>
                <input type="text" class="form-control" id="nama_divisi" name="nama_divisi"
                    value="{{ old('nama_divisi', $divisi->nama_divisi) }}"
                    placeholder="Masukkan nama" required>
            </div>
            <div class="form-group">
                <label for="mingguan">Mingguan</label>
                <input type="text" class="form-control" id="mingguan" name="mingguan"
                    value="{{ old('mingguan', $divisi->mingguan) }}" placeholder="Apakah divisi ini mendapatkan uang mingguan?" required>
            </div>
            <div class="form-group">
                <label for="kedatangan">Kedatangan</label>
                <input type="text" class="form-control" id="kedatangan" name="kedatangan"
                    value="{{ old('kedatangan', $divisi->kedatangan) }}" placeholder="Apakah divisi ini wajib presensi dan mendapatkan uang mingguan?" required>
            </div>
        </div>
        <!-- /.card-body -->

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Update</button>
            <!-- Tombol Batal -->
            <a href="{{ url()->previous() }}" class="btn btn-danger">Batal</a>
        </div>
    </form>
</div>
<!-- /.card -->

@endsection