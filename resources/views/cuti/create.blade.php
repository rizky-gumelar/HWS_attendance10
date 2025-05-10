@php
$layout = auth()->user()->role === 'admin' ? 'layouts.manage' : 'layouts.spv_manage';
$isAdmin = auth()->user()->role === 'admin';
@endphp

@extends($layout)

@section('title', 'Pengajuan Cuti')

@section('page-title', 'Pengajuan Cuti')

@section('content')


<!-- general form elements -->
<div class="card card-primary m-4">
    <div class="card-header">
        <h3 class="card-title">Pengajuan Cuti</h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
    <form action="{{ route('cuti.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label>Nama Karyawan</label>
                @if(!$isAdmin)
                <input type="text" class="form-control" value="{{ auth()->user()->nama_karyawan }}" readonly>
                @else
                <select class="form-control select2" id="user_id" name="user_id" required>
                    <option value="" disabled selected>Pilih Karyawan</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->nama_karyawan }}</option>
                    @endforeach
                </select>
                @error('user_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @endif
            </div>
            <div class="form-group">
                <label>Jenis Cuti</label>
                <select class="form-control" id="jenis_cuti_id" name="jenis_cuti_id" required>
                    <option value="" disabled selected>Pilih Jenis Cuti</option>
                    @foreach($jenisCuti as $jenis)
                    <option value="{{ $jenis->id }}">{{ $jenis->nama_cuti }}</option>
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
            <div class="form-group">
                <label for="imagename">Upload Image</label>
                <input type="file" name="imagename" id="imagename" class="form-control" accept="image/jpeg,image/png,image/jpg">
                @error('imagename')
                <small class="text-danger">{{ $message }}</small>
                @enderror
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


@endsection