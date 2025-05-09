@php
$layout = auth()->user()->role === 'admin' ? 'layouts.manage' : 'layouts.spv_manage';
@endphp

@extends($layout)

@section('title', 'Lembur')

@section('page-title', 'Lembur')

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
                                <input type="file" name="csv_file" accept=".csv,.xlsx" required>
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
                    <!-- <form action="{{ route('lembur.index') }}" method="GET">
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
                    </form> -->


                    <button type="button" class="btn btn-info  mb-4" data-bs-toggle="modal" data-bs-target="#importModal">
                        Upload Lembur
                    </button>
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Tipe Lembur</th>
                                <th>Total biaya</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($grouplembur as $group)
                            <tr>
                                <td>{{ $group->tanggal }}</td>
                                <td>{{ $group->lembur->tipe_lembur  ?? 'Tidak Ada Lembur'  }}</td>
                                <td>{{ $group->total  }}</td>
                                <td>
                                    <button class="btn btn-primary btn-detail"
                                        data-tanggal="{{ $group->tanggal }}"
                                        data-lembur="{{ $group->lembur_id }}">
                                        Detail
                                    </button>
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

<!-- Modal -->
<div class="modal fade" id="jadwalModal" tabindex="-1" role="dialog" aria-labelledby="jadwalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail <!-- ur --></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" id="jadwalDetailTable">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama</th>
                            <th>Tipe Lembur</th>
                            <th>Durasi</th>
                            <th>Total</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Script -->
<script>
    const deleteLemburUrl = "{{ url('lembur/delete') }}";
    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.btn-detail');
        buttons.forEach(button => {
            button.addEventListener('click', function() {
                const tanggal = this.getAttribute('data-tanggal');
                const lembur_id = this.getAttribute('data-lembur');

                fetch(`/lembur/detail?tanggal=${tanggal}&lembur_id=${lembur_id}`)
                    .then(response => response.json())
                    .then(data => {
                        const tbody = document.querySelector('#jadwalDetailTable tbody');
                        tbody.innerHTML = '';

                        // Clear existing DataTable instance if exists
                        if ($.fn.DataTable.isDataTable('#jadwalDetailTable')) {
                            $('#jadwalDetailTable').DataTable().clear().destroy();
                        }

                        if (data.jadwal.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="6">Tidak ada data</td></tr>';
                        } else {
                            data.jadwal.forEach(item => {
                                tbody.innerHTML += `
            <tr>
                <td>${item.tanggal || 'Tanggal Tidak Ditemukan'}</td>
                <td>${item.users?.nama_karyawan || 'User Tidak Ditemukan'}</td>
                <td>${item.lembur?.tipe_lembur || 'Lembur Tidak Ditemukan'}</td>
                <td>${item.lembur_jam || '-'}</td>
                <td>${item.total_lembur || '-'}</td>
                <td>${item.keterangan || '-'}</td>
                <td>
                    <form action="${deleteLemburUrl}/${item.id}" method="POST" class="d-inline">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="PUT">
                        <button class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus lembur ini?')">Delete</button>
                    </form>
                </td>
            </tr>`;
                            });
                        }

                        // Apply DataTables
                        $('#jadwalDetailTable').DataTable({
                            responsive: true,
                            destroy: true,
                            lengthChange: false,
                            autoWidth: false,
                            pageLength: 15,
                            order: [
                                [0, "desc"]
                            ],
                            buttons: ["copy", "csv", "excel", "pdf", "print", "colvis"]
                        }).buttons().container().appendTo('#jadwalModal .modal-body .col-md-6:eq(0)');

                        // Tampilkan modal
                        $('#jadwalModal').modal('show');
                    });
            });
        });
    });
</script>

@endsection