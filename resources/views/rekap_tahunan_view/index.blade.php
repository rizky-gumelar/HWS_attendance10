@php
$layout = auth()->user()->role === 'admin' ? 'layouts.manage' : 'layouts.spv_manage';
@endphp

@extends($layout)

@section('title', 'Rekap Tahunan')

@section('page-title', 'Rekap Tahunan')

@section('content')
<!-- Main content -->

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
                        <form action="{{ route('rekap_tahunan.index') }}" method="GET" class="mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="tahun">Pilih Tahun:</label>
                                    <select name="tahun" id="tahun" class="form-control">
                                        @foreach(range(2020, Carbon\Carbon::now()->year) as $year)
                                        <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary mt-4">Tampilkan</button>
                                </div>
                            </div>
                        </form>

                        <a href="{{ route('rekap_tahunan.generate', request('tahun') ?? Carbon\Carbon::now()->year) }}" class="btn btn-success my-4">Generate Rekap</a>

                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID User</th>
                                    <th>Nama User</th>
                                    <th>Cuti</th>
                                    <th>CF</th>
                                    <th>Sakit</th>
                                    <th>Setengah Hari</th>
                                    <th>Saldo Cuti</th>
                                    <th>Poin Ketidakhadiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rekap as $data)
                                <tr>
                                    <td>{{ $data->user_id }}</td>
                                    <td>{{ $data->user->name }}</td>
                                    <td>{{ $data->cuti }}</td>
                                    <td>{{ $data->cf }}</td>
                                    <td>{{ $data->sakit }}</td>
                                    <td>{{ $data->setengah_hari }}</td>
                                    <td>{{ $data->saldo_cuti }}</td>
                                    <td>{{ $data->poin_ketidakhadiran }}</td>

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