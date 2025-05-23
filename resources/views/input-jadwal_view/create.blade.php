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
        <h3 class="card-title">Input Jadwal Karyawan</h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
    <form action="{{ route('input-jadwal.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label for="user_id">Nama Karyawan</label>
                <select class="form-control select2" id="user_id" name="user_id" required>
                    <option value="" disabled selected>Pilih Karyawan</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->nama_karyawan }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Shift</label>
                <select class="form-control" id="shift_id" name="shift_id" required>
                    <option value="" disabled selected>Pilih Shift</option>
                    @foreach($shifts as $shift)
                    @if ($shift->id >= 1 && $shift->id <= 1000)
                        <option value="{{ $shift->id }}">{{ $shift->nama_shift }}</option>
                        @endif
                        @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="tanggal">Tanggal</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" required>
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