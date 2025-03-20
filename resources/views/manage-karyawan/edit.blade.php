@extends('layouts.manage')

@section('title', 'Manage')

@section('page-title', 'Manage')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- general form elements -->
    <div class="card card-primary m-4">
        <div class="card-header">
            <h3 class="card-title">Edit Karyawan</h3>
        </div>
        <!-- /.card-header -->
        <!-- form start -->
        <form action="{{ route('manage-karyawan.update', $karyawan->id) }}" method="POST">
            @csrf
            @method('PUT') <!-- Laravel membutuhkan method PUT untuk update -->

            <div class="card-body">
                <div class="form-group">
                    <label for="nama_karyawan">Nama Karyawan</label>
                    <input type="text" class="form-control" id="nama_karyawan" name="nama_karyawan"
                        value="{{ old('nama_karyawan', $karyawan->nama_karyawan) }}"
                        placeholder="Masukkan nama" required>
                </div>

                <div class="form-group">
                    <label>Toko</label>
                    <select class="form-control" name="toko_id" required>
                        <option value="1" {{ $karyawan->toko_id == 1 ? 'selected' : '' }}>HWS</option>
                        <option value="2" {{ $karyawan->toko_id == 2 ? 'selected' : '' }}>HIJ</option>
                        <option value="3" {{ $karyawan->toko_id == 3 ? 'selected' : '' }}>SS</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="divisi">Divisi</label>
                    <input type="text" class="form-control" id="divisi" name="divisi"
                        value="{{ old('divisi', $karyawan->divisi) }}"
                        placeholder="Masukkan divisi" required>
                </div>

                <div class="form-group">
                    <label for="no_hp">Nomor Handphone</label>
                    <input type="text" class="form-control" id="no_hp" name="no_hp"
                        value="{{ old('no_hp', $karyawan->no_hp) }}"
                        placeholder="Masukkan Nomor Handphone">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email"
                        value="{{ old('email', $karyawan->email) }}"
                        placeholder="Masukkan Email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password (kosongkan jika tidak ingin mengubah)</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password baru">
                </div>

                <div class="form-group">
                    <label>Role</label>
                    <select class="form-control" name="role" required>
                        <option value="admin" {{ $karyawan->role == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="spv" {{ $karyawan->role == 'spv' ? 'selected' : '' }}>Supervisor</option>
                        <option value="karyawan" {{ $karyawan->role == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                    </select>
                </div>
            </div>
            <!-- /.card-body -->

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
    <!-- /.card -->
</div>
@endsection