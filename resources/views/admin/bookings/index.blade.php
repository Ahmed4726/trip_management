@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
    <div class="container pt-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">Manage Booking</h2>
            @can('create-trip')
            <a href="{{ route('bookings.create') }}" class="btn btn-primary">Create</a>
            @endcan
        </div>
            @if(session('success'))
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: '{{ session('success') }}',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                </script>
            @endif

        <!-- Filters -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label>Boat</label>
                        <select id="filterBoat" class="form-control">
                            <option value="">All boats</option>
                            @foreach($boats as $boat)
                                <option value="{{ $boat->name }}">{{ $boat->name }} ({{ $boat->rooms->count() }} rooms)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Status</label>
                        <select id="filterStatus" class="form-control">
                            <option value="">All statuses</option>
                            <option value="Available">Available</option>
                            <option value="Partially Booked">Partially Booked</option>
                            <option value="Fully Booked">Fully Booked</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Start Date</label>
                        <input type="date" id="filterStartDate" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>End Date</label>
                        <input type="date" id="filterEndDate" class="form-control">
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendars -->
        <div class="row">
            @for($i = 0; $i < 4; $i++)
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm p-2">
                        <div id="calendar-{{ $i }}"></div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.15/index.global.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* Modern Event Colors */
    .fc-event {
        border-radius: 6px !important;
        font-weight: 500;
        color: #fff !important;
        cursor: pointer;
    }
    .available-event { background: #28a745; }
    .partial-event { background: #ffc107; }
    .full-event { background: #dc3545; }
</style>

<script>
let calendars = [];

function getFilters() {
    return {
        boat: $('#filterBoat').val(),
        status: $('#filterStatus').val(),
        start_date: $('#filterStartDate').val(),
        end_date: $('#filterEndDate').val()
    };
}

function loadCalendars() {
    calendars.forEach(cal => cal.destroy());
    calendars = [];

    for (let i = 0; i < 4; i++) {
        let calendarEl = document.getElementById('calendar-' + i);

        let calendar = new FullCalendar.Calendar(calendarEl, {
            timeZone: 'local',
            initialView: 'dayGridMonth',
            initialDate: new Date(new Date().setMonth(new Date().getMonth() + i)),
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth'
            },
            height: 550,
            nowIndicator: true,
            events: {
                url: "{{ route('booking.events') }}",
                method: 'GET',
                extraParams: getFilters
            },
            eventDidMount: function(info) {
                const booked = info.event.extendedProps.booked;
                const total = info.event.extendedProps.total_rooms;

                if (booked == 0) info.el.classList.add('available-event');
                else if (booked < total) info.el.classList.add('partial-event');
                else info.el.classList.add('full-event');

                // Tooltip
                let tooltip = `Booked: ${booked} / ${total}\nAvailable: ${info.event.extendedProps.available}`;
                info.el.setAttribute('title', tooltip);
            },
            eventClick: function(info) {
                const tripId = info.event.extendedProps.trip_id;
                const available = info.event.extendedProps.available;
                const startDate = info.event.extendedProps.start_date; // real start date
                const endDate = info.event.extendedProps.end_date;     // real end date
                const boatName = info.event.extendedProps.boat_name;

                $.get(`/trip/${tripId}/rooms`, function(data) {
                    let html = `<b>Boat:</b> ${boatName} <br>
                                <b>Start:</b> ${startDate} <br>
                                <b>End:</b> ${endDate} <br>
                                <ul>`;
                    data.rooms.forEach(r => {
                        let color = r.is_booked ? 'red' : 'green';
                        html += `<li style="color:${color}">${r.name} (${r.capacity}) - ${r.is_booked ? 'Booked' : 'Available'}</li>`;
                    });
                    html += '</ul>';

                    Swal.fire({
                        title: `<strong>${info.event.title}</strong>`,
                        html: html,
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonText: 'Close',
                        cancelButtonText: 'View Bookings',
                        showDenyButton: available > 0,
                        denyButtonText: 'Create Booking'
                    }).then(result => {
                        if (result.isDenied) {
                            window.location.href = `/create-booking?trip_id=${tripId}`;
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            window.location.href = `/booking/${tripId}`;
                        }
                    });
                });
            }
        });

        calendar.render();
        calendars.push(calendar);
    }
}
 

$('#filterBoat, #filterStatus, #filterStartDate, #filterEndDate').on('change', loadCalendars);

$(document).ready(loadCalendars);
</script>
@endsection
