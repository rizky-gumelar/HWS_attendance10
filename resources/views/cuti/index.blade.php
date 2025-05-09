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

@php

$percentCuti = ($user->total_cuti != 0) ? $user->sisa_cuti / $user->total_cuti * 100 : 0;
$percentPoin = ($user->poin_tidak_hadir != 0) ? $user->poin_terakhir / $user->poin_tidak_hadir * 100 : 0;
@endphp

@extends($layout)

@section('title', 'Cuti')

@section('page-title', 'Cuti')

@section('content')

@foreach($pengajuanCuti as $cuti)
<!-- Modal -->
<div class="modal fade" id="imageModal{{ $cuti->id }}" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="imageModalLabel">Bukti Cuti - {{ $cuti->users->nama_karyawan }} / {{ $cuti->tanggal }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body mx-auto">
                <img src="{{ asset('storage/cuti/' . $cuti->imagename) }}" alt="Bukti Cuti" style="max-width: 500px;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Cuti</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Cuti</li>
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
                        <div class="row">
                            <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                                <div class="info-box bg-info shadow-none">
                                    <span class="info-box-icon"><i class="fas fa-outdent"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">Sisa Saldo Cuti</span>
                                        <span class="info-box-number">{{ $user->sisa_cuti }} / {{$user->total_cuti}}</span>

                                        <div class="progress">
                                            <div class="progress-bar" style="width: {{ $percentCuti }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                                <div class="info-box bg-success shadow-none">
                                    <span class="info-box-icon"><i class="fas fa-outdent"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">Sisa Poin Tidak Hadir</span>
                                        <span class="info-box-number">{{ $user->poin_terakhir }} / {{$user->poin_tidak_hadir}} </span>

                                        <div class="progress">
                                            <div class="progress-bar" style="width: {{ $percentPoin }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('cuti.create') }}" class="btn btn-success mt-2">Buat pengajuan</a>
                    </div>
                    <div class="card-body">
                        <h3>Riwayat Cuti</h3>
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Jenis Cuti</th>
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                    <th>Foto Surat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pengajuanCuti as $cuti)
                                <tr>
                                    <td>{{ $cuti->users->nama_karyawan }}</td>
                                    <td>{{ $cuti->jenis_cuti->nama_cuti }}</td>
                                    <td>{{ $cuti->tanggal }}</td>
                                    <td>{{ $cuti->keterangan }}</td>
                                    <td>
                                        @if($cuti->status == 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                        @elseif($cuti->status == 'disetujui admin')
                                        <span class="badge badge-success">Disetujui Admin</span>
                                        @elseif($cuti->status == 'disetujui spv')
                                        <span class="badge badge-success">Disetujui SPV</span>
                                        <span class="badge badge-warning">Menunggu persetujuan Admin</span>
                                        @elseif($cuti->status == 'batal')
                                        <span class="badge badge-warning">Dibatalkan</span>
                                        @else
                                        <span class="badge badge-danger">Ditolak</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($cuti->imagename)
                                        <a href="" data-bs-toggle="modal" data-bs-target="#imageModal{{ $cuti->id }}">
                                            <img src="{{ asset('storage/cuti/' . $cuti->imagename) }}" alt="Bukti Cuti" style="max-width: 100px; max-height: 100px;">
                                        </a>
                                        @else
                                        Tidak ada gambar
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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