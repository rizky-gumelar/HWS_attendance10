@extends('layouts.manage')

@section('title', 'Manage')

@section('page-title', 'Manage')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- general form elements -->
    <div class="card card-primary m-4">
        <div class="card-header">
            <h3 class="card-title">Edit Jadwal</h3>
        </div>
        <!-- /.card-header -->
        <!-- form start -->
        <form action="{{ route('input-jadwal.update', $input_jadwal->id) }}" method="POST">
            @csrf
            @method('PUT') <!-- Laravel membutuhkan method PUT untuk update -->

            <div class="card-body">
                <div class="form-group">
                    <label for="user_id">Nama Karyawan</label>
                    <select class="form-control" id="user_id" name="user_id" required>
                        <option value="" disabled selected>Pilih Karyawan</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ $user->nama_karyawan ? 'selected' : '' }}>{{ $user->nama_karyawan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Shift</label>
                    <select class="form-control" id="shift_id" name="shift_id" required>
                        <option value="" disabled selected>Pilih Shift</option>
                        @foreach($shifts as $shift)
                        <option value="{{ $shift->id }}" {{ $shift->nama_shift ? 'selected' : '' }}>{{ $shift->nama_shift }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Jenis Lembur</label>
                    <select class="form-control" id="lembur_id" name="lembur_id" required>
                        <option value="" disabled selected>Pilih Jenis Lembur</option>
                        @foreach($lemburs as $lembur)
                        <option value="{{ $lembur->id }}" {{ $lembur->nama_lembur ? 'selected' : '' }}>{{ $lembur->nama_lembur }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="tanggal">Tanggal</label>
                    <input type="date" value="{{ $input_jadwal->tanggal }}" class="form-control" id="tanggal" name="tanggal" required>
                </div>
                <div class="form-group">
                    <label for="alamat">Jam Lembur</label>
                    <input type="numeric" class="form-control" id="lembur_jam" name="lembur_jam" value="{{ old('lembur_jam', $input_jadwal->lembur_jam) }}" required>
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
</div>
@endsection