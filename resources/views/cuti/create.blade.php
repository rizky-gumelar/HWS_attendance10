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
        <div class="card-header">
            <h3 class="card-title">Pengajuan Cuti</h3>
        </div>
        <!-- /.card-header -->
        <!-- form start -->
        <form action="{{ route('cuti.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label>Text Disabled</label>
                    <input type="text" class="form-control" placeholder="Enter ..." disabled>
                </div>
                <div class="form-group">
                    <label>Jenis Cuti</label>
                    <select class="form-control" id="jenis_cuti_id" name="jenis_cuti_id" required>
                        <option value="" disabled selected>Pilih Jenis Cuti</option>
                        @foreach($jenisCuti as $jenis)
                        <option value="{{ $jenis_cuti->id }}">{{ $jenis_cuti->nama_cuti }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="tanggal">Tanggal</label>
                    <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                </div>
                <div class="form-group">
                    <label for="keterangan">Keterangan</label>
                    <input type="text" class="form-control" id="keterangan" name="keterangan" required>
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

</div>
@endsection