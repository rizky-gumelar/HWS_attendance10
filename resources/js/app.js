import './bootstrap';
import { Calendar } from '@fullcalendar/core';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';

const calendar = new Calendar(document.getElementById('calendar'), {
    plugins: [timeGridPlugin, interactionPlugin],
    initialView: 'timeGridWeek',
    events: '/calendar', // atau /jadwal_karyawan
    slotMinTime: "06:00:00",
    slotMaxTime: "24:00:00",
});