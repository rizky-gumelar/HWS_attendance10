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
            <h3 class="card-title">Tambah Karyawan</h3>
        </div>
        <!-- /.card-header -->
        <!-- form start -->
        <form action="{{ route('manage-karyawan.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="nama_karyawan">Nama Karyawan</label>
                    <input type="text" class="form-control" id="nama_karyawan" name="nama_karyawan" placeholder="Masukkan nama" required>
                </div>
                <div class="form-group">
                    <label>Toko</label>
                    <select class="form-control" id="toko_id" name="toko_id" required>
                        <option value="" disabled selected>Pilih Toko</option>
                        @foreach($tokos as $toko)
                        <option value="{{ $toko->id }}">{{ $toko->nama_toko }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Shift Default</label>
                    <select class="form-control" id="default_shift_id" name="default_shift_id" required>
                        <option value="" disabled selected>Pilih Shift</option>
                        @foreach($shifts as $shift)
                        <option value="{{ $shift->id }}">{{ $shift->nama_shift }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Divisi</label>
                    <select class="form-control" id="divisi_id" name="divisi_id" required>
                        <option value="" disabled selected>Pilih Divisi</option>
                        @foreach($divisis as $divisi)
                        <option value="{{ $divisi->id }}">{{ $divisi->nama_divisi }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="no_hp">Nomor Handphone</label>
                    <input type="text" class="form-control" id="no_hp" name="no_hp" placeholder="Masukkan Nomor Handphone">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan Email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select class="form-control" name="role" required>
                        <option value="admin">Admin</option>
                        <option value="spv">Supervisor</option>
                        <option value="karyawan">Karyawan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" name="status" required>
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
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