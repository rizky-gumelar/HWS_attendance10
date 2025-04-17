@php
$layout = auth()->user()->role === 'admin' ? 'layouts.manage' : 'layouts.spv_manage';
@endphp

@extends($layout)

@section('title', 'Manage')

@section('page-title', 'Manage')

@section('content')

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
                    <option value="{{ $user->id }}" {{ $user->id == $input_jadwal->user_id ? 'selected' : '' }}>{{ $user->nama_karyawan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Shift</label>
                <select class="form-control" id="shift_id" name="shift_id" required>
                    <option value="" disabled selected>Pilih Shift</option>
                    @foreach($shifts as $shift)
                    @if ($shift->id >= 1 && $shift->id <= 1000)
                        <option value="{{ $shift->id }}" {{ $shift->id == $input_jadwal->shift_id ? 'selected' : '' }}>{{ $shift->nama_shift }}</option>
                        @endif
                        @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Jenis Lembur</label>
                <select class="form-control" id="lembur_id" name="lembur_id">
                    <option value="No" selected>Pilih Jenis Lembur</option>
                    <option value="No">-</option>
                    @foreach($lemburs as $lembur)
                    <option value="{{ $lembur->id }}" {{ $lembur->id == $input_jadwal->lembur_id ? 'selected' : '' }}>{{ $lembur->tipe_lembur }}</option>
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

@endsection