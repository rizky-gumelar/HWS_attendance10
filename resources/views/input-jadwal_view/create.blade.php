@extends('layouts.manage')

@section('title', 'Manage')

@section('page-title', 'Manage')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
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
                    <select class="form-control" id="user_id" name="user_id" required>
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
                        <option value="{{ $shift->id }}">{{ $shift->nama_shift }}</option>
                        @endforeach
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
</div>
@endsection