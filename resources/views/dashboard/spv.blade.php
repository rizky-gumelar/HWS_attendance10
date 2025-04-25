<!-- <h1>Supervisor Dashboard</h1>
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Logout</button>
</form> -->

@extends('layouts.spv_manage')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section('content')
<style>
    #calendar .fc-event-title {
        color: black !important;
        font-weight: normal !important;
    }
</style>
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Dashboard</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card p-5">
                    <!-- Small boxes (Stat box) -->
                    <div id="calendar" class="fc-event-title " style="color: black; font-weight: normal;"></div>
                    <!-- /.row -->
                </div>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>


<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.4/index.global.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            events: '/calendar', // ganti sesuai route kamu
            timeZone: 'local', // Pastikan sesuai kebutuhanmu
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            },
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            displayEventTime: false,

        });

        calendar.render();
    });
</script>
@endsection