@php
$role = auth()->user()->role;

if (in_array($role, ['admin', 'super_admin'])) {
$layout = 'layouts.manage';
} elseif (in_array($role, ['spv', 'supervisor'])) {
$layout = 'layouts.spv_manage';
} else {
$layout = 'layouts.karyawan_manage'; // fallback jika peran tidak cocok
}
@endphp

@extends($layout)

@section('title', 'Manage Karyawan')

@section('page-title', 'Manage Karyawan')

@section('content')

<!-- general form elements -->
<div class="card card-primary m-4">
    <div class="card-header">
        <h3 class="card-title">Ubah Password</h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
    <form action="{{ route('manage-karyawan.update-password') }}" method="POST">
        @csrf
        @method('PUT') <!-- Laravel membutuhkan method PUT untuk update -->
        <div class="card-body">
            <div class="form-group">
                <label>Password Baru</label>
                <input class="form-control" type="password" name="password" required>
            </div>

            <div class="form-group">
                <label>Konfirmasi Password</label>
                <input class="form-control" type="password" name="password_confirmation" required>
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