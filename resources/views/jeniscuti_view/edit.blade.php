@extends('layouts.manage')

@section('title', 'Manage')

@section('page-title', 'Manage')

@section('content')

<!-- general form elements -->
<div class="card card-primary m-4">
    <div class="card-header">
        <h3 class="card-title">Edit Jenis Cuti</h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
    <form action="{{ route('jenis-cuti.update', $jeniscuti->id) }}" method="POST">
        @csrf
        @method('PUT') <!-- Laravel membutuhkan method PUT untuk update -->

        <div class="card-body">
            <div class="form-group">
                <label for="nama_cuti">Nama Cuti</label>
                <input type="text" class="form-control" id="nama_cuti" name="nama_cuti" placeholder="Masukkan nama" value="{{ old('nama_cuti', $jeniscuti->nama_cuti) }}" required>
            </div>
            <div class="form-group">
                <label>Potong saldo cuti</label>
                <select class="form-control" name="status" required>
                    <option value="1" {{ $jeniscuti->status == 1 ? 'selected' : '' }}>Potong saldo</option>
                    <option value="0" {{ $jeniscuti->status == 0 ? 'selected' : '' }}>Tidak</option>
                    <option value="0.5" {{ $jeniscuti->status == 0.5 ? 'selected' : '' }}>Potong setengah</option>
                </select>
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