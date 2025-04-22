@extends('layouts.manage')

@section('title', 'Manage Karyawan')

@section('page-title', 'Manage Karyawan')

@section('content')
<!-- Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="importModalLabel">Upload File CSV</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Download template excel :
                    <a href="{{ route('export-template-lembur') }}">Template Excel</a>
                </p>
                <form action="{{ route('lembur.import-lembur') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="exampleInputFile">File input</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="csv_file" accept=".csv" required>
                                <button type="submit" class="input-group-text">Impor Lembur</button>
                            </div>
                            <div class="input-group-append">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<!-- Content Wrapper. Contains page content -->
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Manage Lembur</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">lembur</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="{{ route('lembur.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="start_date">Dari Tanggal:</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="end_date">Sampai Tanggal:</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">Cari</button>
                                <a href="{{ route('lembur.index') }}" class="btn btn-secondary ml-2">Reset</a>
                            </div>
                        </div>
                    </form>


                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#importModal">
                        Upload Lembur
                    </button>
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>ID Karyawan</th>
                                <th>Nama Karyawan</th>
                                <th>Tanggal</th>
                                <th>Jam Masuk</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lemburs as $lembur)
                            <tr>

                                <td>
                                    <a href="{{ route('lembur.edit', $lembur->id) }}" class="btn btn-warning">Edit</a>
                                    <form action="{{ route('lembur.destroy', $lembur->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
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

<!-- /.content -->
@endsection