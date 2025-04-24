@php
$layout = auth()->user()->role === 'admin' ? 'layouts.manage' : 'layouts.spv_manage';
@endphp

@extends($layout)

@section('title', 'Manage Karyawan')

@section('page-title', 'Manage Karyawan')

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
                    <th>Foto Surat</th>
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
                    <td>
                        @if($cuti->status == 'pending')
                        @if(auth()->user()->role === 'admin')
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
                        @elseif(auth()->user()->role === 'spv')
                        <form action="{{ route('cuti.spv_approve', $cuti->id) }}" method="POST" style="display:inline-block;">
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
                        @endif
                        @elseif($cuti->status == 'disetujui spv')
                        @if(auth()->user()->role === 'admin')
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
                        @elseif(auth()->user()->role === 'spv')
                        <form action="{{ route('cuti.cancel', $cuti->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button class="btn btn-sm btn-warning" onclick="return confirm('Batalkan cuti ini?')">Batalkan</button>
                        </form>
                        @endif
                        @elseif($cuti->status == 'disetujui admin')
                        @if(auth()->user()->role === 'admin')
                        <form action="{{ route('cuti.cancel', $cuti->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button class="btn btn-sm btn-warning" onclick="return confirm('Batalkan cuti ini?')">Batalkan</button>
                        </form>
                        @elseif(auth()->user()->role === 'spv')
                        <em>-</em>
                        @endif
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