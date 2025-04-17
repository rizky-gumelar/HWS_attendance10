@php
$layout = auth()->user()->role === 'admin' ? 'layouts.manage' : 'layouts.spv_manage';
@endphp

@extends($layout)

@section('title', 'Manage Karyawan')

@section('page-title', 'Manage Karyawan')

@section('content')

<!-- general form elements -->
<div class="card card-primary m-4">

    <div class="card-body">
        <table class="table table-bordered table-striped">
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
                        @elseif($cuti->status == 'batal')
                        <span class="badge badge-warning">Dibatalkan</span>
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
                        <form action="{{ route('cuti.cancel', $cuti->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button class="btn btn-sm btn-warning" onclick="return confirm('Batalkan cuti ini?')">Batalkan</button>
                        </form>
                        @elseif($cuti->status == 'disetujui spv')
                        <form action="{{ route('cuti.cancel', $cuti->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button class="btn btn-sm btn-warning" onclick="return confirm('Batalkan cuti ini?')">Batalkan</button>
                        </form>
                        @elseif($cuti->status == 'disetujui admin')
                        <form action="{{ route('cuti.cancel', $cuti->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button class="btn btn-sm btn-warning" onclick="return confirm('Batalkan cuti ini?')">Batalkan</button>
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
</div>
<!-- /.card -->

@endsection