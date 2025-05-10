@php
$layout = auth()->user()->role === 'admin' ? 'layouts.manage' : 'layouts.spv_manage';
@endphp

@extends($layout)

@section('title', 'Rekap Tahunan')

@section('page-title', 'Rekap Tahunan')

@section('content')
<!-- Main content -->
<style>
    .col-group-1 {
        background-color: #f8f9fa !important;
    }

    /* Abu muda */
    .col-group-2 {
        background-color: #e9f7ef !important;
    }

    /* Hijau muda */
    .col-group-3 {
        background-color: #fff3cd !important;
    }

    /* Kuning muda */
    .col-group-4 {
        background-color: #fde2e2 !important;
    }

    .col-group-5 {
        background-color: rgb(226, 239, 253) !important;
    }

    /* Merah muda */
</style>

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Rekap Tahunan</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Rekap Tahunan</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <!-- /.card-header -->
                    <div class="card-body">

                        <!-- Filter Tahun -->
                        <!-- Filter Tahun -->
                        <form action="{{ route('rekap_tahunan.index') }}" method="GET" class="mb-4">
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <label for="tahun" class="form-label">Pilih Tahun:</label>
                                    <select name="tahun" id="tahun" class="form-select">
                                        @php
                                        $currentYear = \Carbon\Carbon::now()->year;
                                        $selectedYear = request('tahun', $currentYear);
                                        @endphp
                                        @foreach(range(2020, $currentYear) as $year)
                                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                                </div>
                            </div>
                        </form>


                        <a href="{{ route('rekap_tahunan.generate', request('tahun') ?? Carbon\Carbon::now()->year) }}" class="btn btn-success my-4">Generate Rekap</a>

                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="col-group-1">ID User</th>
                                    <th class="col-group-1">Nama User</th>
                                    <th class="col-group-2">Cuti</th>
                                    <th class="col-group-2">CF</th>
                                    <th class="col-group-2">Sakit</th>
                                    <th class="col-group-2">Setengah Hari</th>
                                    <th class="col-group-2">Terlambat</th>
                                    <th class="col-group-3">Saldo Cuti</th>
                                    <th class="col-group-3">Poin Tidak Hadir</th>
                                    <th class="col-group-4">Cuti Terpakai</th>
                                    <th class="col-group-4">Poin Terpakai</th>
                                    <th class="col-group-5">Sisa Cuti</th>
                                    <th class="col-group-5">Sisa Poin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rekap as $data)
                                <tr>
                                    <td class="col-group-1">{{ $data->user_id }}</td>
                                    <td class="col-group-1">{{ $data->user->nama_karyawan }}</td>
                                    <td class="col-group-2">{{ $data->cuti }}</td>
                                    <td class="col-group-2">{{ $data->cf }}</td>
                                    <td class="col-group-2">{{ $data->sakit }}</td>
                                    <td class="col-group-2">{{ $data->setengah_hari }}</td>
                                    <td class="col-group-2">{{ $data->terlambat }}</td>
                                    <td class="col-group-3">{{ $data->saldo_cuti }}</td>
                                    <td class="col-group-3">{{ $data->poin_ketidakhadiran }}</td>
                                    <td class="col-group-4">{{ $data->cuti_terpakai }}</td>
                                    <td class="col-group-4">{{ $data->poin_terpakai }}</td>
                                    <td class="col-group-5">{{ $data->cuti_akhir }}</td>
                                    <td class="col-group-5">{{ $data->poin_akhir }}</td>
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
<!-- /.content -->

@endsection