<!-- <h1>Karyawan Dashboard</h1>
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Logout</button>
</form> -->
@php
$saldo = auth()->user()->total_cuti;
$percent = $saldo/24*100;
@endphp
@extends('layouts.karyawan_manage')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fas fa-outdent"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Saldo Cuti</span>
                    <span class="info-box-number">{{ $saldo }}</span>

                    <div class="progress">
                        <div class="progress-bar" style="width: {{ $percent }}%"></div>
                    </div>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
    </div>
</section>

@endsection