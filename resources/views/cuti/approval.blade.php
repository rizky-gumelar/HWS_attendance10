@php
$layout = auth()->user()->role === 'admin' ? 'layouts.manage' : 'layouts.spv_manage';
@endphp

@extends($layout)

@section('title', 'Manage Karyawan')

@section('page-title', 'Manage Karyawan')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- general form elements -->
    <div class="card card-primary m-4">
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Jenis Cuti</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pengajuanCuti as $cuti)
                <tr>
                    <td>{{ $cuti->users->nama_karyawan }}</td>
                    <td>{{ $cuti->jenisCuti->nama_cuti }}</td>
                    <td>{{ $cuti->tanggal }}</td>
                    <td>{{ $cuti->keterangan }}</td>
                    <td>
                        @if($cuti->status == 'pending')
                        <span class="badge badge-warning">Pending</span>
                        @elseif($cuti->status == 'approve')
                        <span class="badge badge-success">Disetujui</span>
                        @else
                        <span class="badge badge-danger">Ditolak</span>
                        @endif
                    </td>
                    <td>
                        @if($cuti->status == 'pending')
                        <form action="{{ route('cuti.approve', $cuti->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button class="btn btn-sm btn-success" onclick="return confirm('Setujui cuti ini?')">Approve</button>
                        </form>
                        <form action="{{ route('cuti.reject', $cuti->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Tolak cuti ini?')">Reject</button>
                        </form>
                        @else
                        <em>-</em>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- /.card -->
</div>
@endsection