@php
$layout = auth()->user()->role === 'admin' ? 'layouts.manage' : 'layouts.spv_manage';
@endphp

@extends($layout)

@section('title', 'Manage')

@section('page-title', 'Manage')

@section('content')

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
            @if(auth()->user()->role === 'admin')
            <div class="form-group">
                <label for="id">ID Karyawan</label>
                <input type="number" class="form-control  @error('id') is-invalid @enderror" id="id" name="id"
                    value="{{ old('id', $karyawan->id) }}"
                    placeholder="Masukkan id" required>
                @error('id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            @else
            <div class="form-group">
                <label for="id">ID Karyawan</label>
                <input type="number" class="form-control  @error('id') is-invalid @enderror" id="id" name="id" disabled
                    value="{{ old('id', $karyawan->id) }}"
                    placeholder="Masukkan id" required>
                @error('id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            @endif
            <div class="form-group">
                <label for="nama_karyawan">Nama Karyawan</label>
                <input type="text" class="form-control  @error('nama_karyawan') is-invalid @enderror" id="nama_karyawan" name="nama_karyawan"
                    value="{{ old('nama_karyawan', $karyawan->nama_karyawan) }}"
                    placeholder="Masukkan nama" required>
                @error('nama_karyawan')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Toko</label>
                <select class="form-control" name="toko_id" required>
                    <option value="" disabled selected>Pilih Toko</option>
                    @foreach($tokos as $toko)
                    <option value="{{ $toko->id }}" {{ $karyawan->toko_id == $toko->id ? 'selected' : '' }}>{{ $toko->nama_toko }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Shift Default</label>
                <select class="form-control" id="default_shift_id" name="default_shift_id" required>
                    <option value="" disabled selected>Pilih Shift</option>
                    @foreach($shifts as $shift)
                    @if ($shift->id >= 1 && $shift->id <= 1000)
                        <option value="{{ $shift->id }}" {{ $karyawan->default_shift_id == $shift->id ? 'selected' : '' }}>{{ $shift->nama_shift }}</option>
                        @endif
                        @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Divisi</label>
                <select class="form-control" id="divisi_id" name="divisi_id" required>
                    <option value="" disabled selected>Pilih Divisi</option>
                    @foreach($divisis as $divisi)
                    <option value="{{ $divisi->id }}" {{$karyawan->divisi_id == $divisi->id ? 'selected' : '' }}>{{ $divisi->nama_divisi }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="no_hp">Nomor Handphone</label>
                <input type="text" class="form-control @error('no_hp') is-invalid @enderror" id="no_hp" name="no_hp"
                    value="{{ old('no_hp', $karyawan->no_hp) }}"
                    placeholder="Masukkan Nomor Handphone">
                @error('no_hp')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control  @error('email') is-invalid @enderror" id="email" name="email"
                    value="{{ old('email', $karyawan->email) }}"
                    placeholder="Masukkan Email" required>
                @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
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
            @if(auth()->user()->role === 'admin')
            <div class="form-group">
                <label for="total_cuti">Total Cuti</label>
                <input type="text" class="form-control  @error('total_cuti') is-invalid @enderror" id="total_cuti" name="total_cuti" placeholder="Masukkan total cuti"
                    value="{{ old('total_cuti', $karyawan->total_cuti) }}" required>
                @error('total_cuti')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            @else
            <div class="form-group">
                <label for="total_cuti">Total Cuti</label>
                <input type="text" class="form-control  @error('total_cuti') is-invalid @enderror" id="total_cuti" name="total_cuti" placeholder="Masukkan total cuti"
                    value="{{ old('total_cuti', $karyawan->total_cuti) }}" disabled required>
                @error('total_cuti')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            @endif
            <div class="form-group">
                <label>Status</label>
                <select class="form-control" name="status" required>
                    <option value="aktif" {{ $karyawan->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ $karyawan->status == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
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