@extends('layouts.manage')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section('content')
<style>
    a .fc-event-title {
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
                    <h3>Kalender Kerja</h3>
                    <select id="filterDivisi" class="form-select" style="width: 200px; margin-bottom: 10px;">
                        <!-- <option value="">Semua Divisi</option> -->
                        @foreach ($divisiList as $divisi)
                        <option value="{{ $divisi->id }}">{{ $divisi->nama_divisi }}</option>
                        @endforeach
                    </select>
                    <!-- Small boxes (Stat box) -->
                    <div id="calendar" class="fc-event-title" style="color: black; font-weight: normal;"></div>
                    <!-- /.row -->
                </div>
                <div class="card p-5">
                    <h3>Kalender Cuti-Libur</h3>
                    <select id="filterDivisi2" class="form-select" style="width: 200px; margin-bottom: 10px;">
                        <!-- <option value="">Semua Divisi</option> -->
                        @foreach ($divisiList as $divisi)
                        <option value="{{ $divisi->id }}">{{ $divisi->nama_divisi }}</option>
                        @endforeach
                    </select>
                    <!-- Small boxes (Stat box) -->
                    <div id="calendar2" class="fc-event-title" style="color: black; font-weight: normal;"></div>
                    <!-- /.row -->
                </div>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>


<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.4/index.global.min.js"></script>
<!-- <script>
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
            }
        });

        calendar.render();
    });
</script> -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var divisiFilter = document.getElementById('filterDivisi');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            themeSystem: 'bootstrap',
            dayMaxEvents: true,
            dayMaxEventRows: true,
            initialView: 'dayGridMonth',
            timeZone: 'local',
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            },
            displayEventTime: false,
            events: function(fetchInfo, successCallback, failureCallback) {
                fetch(`/calendar?divisi_id=${divisiFilter.value}`)
                    .then(response => response.json())
                    .then(data => successCallback(data))
                    .catch(error => failureCallback(error));
            },
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
        });

        calendar.render();

        // Update event saat filter berubah
        divisiFilter.addEventListener('change', function() {
            calendar.refetchEvents();
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar2');
        var divisiFilter = document.getElementById('filterDivisi2');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            themeSystem: 'bootstrap',
            initialView: 'dayGridMonth',
            dayMaxEvents: true,
            dayMaxEventRows: true,
            timeZone: 'local',
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            },
            displayEventTime: false,
            events: function(fetchInfo, successCallback, failureCallback) {
                fetch(`/calendar-libur?divisi_id=${divisiFilter.value}`)
                    .then(response => response.json())
                    .then(data => successCallback(data))
                    .catch(error => failureCallback(error));
            },
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
        });


        calendar.render();

        // Update event saat filter berubah
        divisiFilter.addEventListener('change', function() {
            calendar.refetchEvents();
        });
    });
</script>

<!-- /.content -->

<!-- /.content-wrapper -->
@endsection