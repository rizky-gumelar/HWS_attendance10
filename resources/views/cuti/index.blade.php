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

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Manage Karyawan</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Manage Karyawan</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>


<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="alert alert-info" role="alert">
                            <strong>Sisa cuti:</strong> {{ $user->total_cuti }}
                        </div>
                        <a href="{{ route('cuti.create') }}" class="btn btn-success my-4">Buat pengajuan</a>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>


@endsection