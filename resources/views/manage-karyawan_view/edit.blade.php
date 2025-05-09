@php
$layout = auth()->user()->role === 'admin' ? 'layouts.manage' : 'layouts.spv_manage';
$isSpv = auth()->user()->role === 'spv';
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
            <div class="form-group">
                <label for="id">ID Karyawan</label>
                <input type="number"
                    {{ $isSpv ? 'readonly' : '' }} class="form-control  @error('id') is-invalid @enderror" id="id" name="id"
                    value="{{ old('id', $karyawan->id) }}"
                    placeholder="Masukkan id" required>
                @error('id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="nama_karyawan">Nama Karyawan</label>
                <input type="text"
                    class="form-control @error('nama_karyawan') is-invalid @enderror"
                    id="nama_karyawan"
                    name="nama_karyawan"
                    value="{{ old('nama_karyawan', $karyawan->nama_karyawan) }}"
                    {{ $isSpv ? 'readonly' : '' }}>

                @error('nama_karyawan')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="tanggal_masuk">Tanggal Masuk</label>
                <input type="date"
                    {{ $isSpv ? 'readonly' : '' }} class="form-control  @error('tanggal_masuk') is-invalid @enderror" id="tanggal_masuk" name="tanggal_masuk" value="{{ old('tanggal_masuk', $karyawan->tanggal_masuk) }}" required>
                @error('tanggal_masuk')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Toko</label>
                @if($isSpv)
                <input type="text" class="form-control" value="{{ $karyawan->toko->nama_toko }}" readonly>
                <input type="hidden" name="toko_id" value="{{ $karyawan->toko_id }}">
                @else
                <select class="form-control @error('toko_id') is-invalid @enderror" name="toko_id" required>
                    <option value="" disabled>Pilih Toko</option>
                    @foreach($tokos as $toko)
                    <option value="{{ $toko->id }}" {{ old('toko_id', $karyawan->toko_id) == $toko->id ? 'selected' : '' }}>
                        {{ $toko->nama_toko }}
                    </option>
                    @endforeach
                </select>
                @error('toko_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @endif
            </div>

            <div class="form-group">
                <label>Shift Default</label>
                <select class="form-control @error('default_shift_id') is-invalid @enderror" name="default_shift_id" required>
                    <option value="" disabled>Pilih Shift</option>
                    @foreach($shifts as $shift)
                    @if ($shift->id >= 1 && $shift->id <= 1000)
                        <option value="{{ $shift->id }}" {{ $karyawan->default_shift_id == $shift->id ? 'selected' : '' }}>{{ $shift->nama_shift }}</option>
                        @endif
                        @endforeach
                </select>
                @error('default_shift_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label>Divisi</label>
                @if($isSpv)
                <input type="text" class="form-control" value="{{ $karyawan->divisi->nama_divisi }}" readonly>
                <input type="hidden" name="divisi_id" value="{{ $karyawan->divisi_id }}">
                @else
                <select class="form-control @error('divisi_id') is-invalid @enderror" name="divisi_id" required>
                    <option value="" disabled selected>Pilih Divisi</option>
                    @foreach($divisis as $divisi)
                    <option value="{{ $divisi->id }}" {{$karyawan->divisi_id == $divisi->id ? 'selected' : '' }}>{{ $divisi->nama_divisi }}</option>
                    @endforeach
                </select>
                @error('divisi_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @endif
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
                @if($isSpv)
                <input type="text" class="form-control" value="{{ $karyawan->role_name }}" readonly>
                <input type="hidden" name="role" value="{{ $karyawan->role }}">
                @else
                <select class="form-control @error('role') is-invalid @enderror" name="role" required>
                    <option value="admin" {{ $karyawan->role == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="spv" {{ $karyawan->role == 'spv' ? 'selected' : '' }}>Supervisor</option>
                    <option value="karyawan" {{ $karyawan->role == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                </select>
                @error('role')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @endif
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
                    value="{{ old('total_cuti', $karyawan->total_cuti) }}" readonly required>
                @error('total_cuti')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            @endif
            <div class="form-group">
                <label>Status</label>
                @if($isSpv)
                <input type="text" class="form-control" value="{{ $karyawan->status }}" readonly>
                <input type="hidden" name="status" value="{{ $karyawan->status }}">
                @else
                <select class="form-control @error('status') is-invalid @enderror" name="status" required>
                    <option value="aktif" {{ $karyawan->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ $karyawan->status == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @endif
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