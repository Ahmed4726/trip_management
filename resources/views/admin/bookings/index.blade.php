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

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label>Boat</label>
                        <select id="filterBoat" class="form-control">
                            <option value="">All boats</option>
                            @foreach($boats as $boat)
                                <option value="{{ $boat->name }} ({{ $boat->rooms->count() }} rooms)">
                                    {{ $boat->name }} ({{ $boat->rooms->count() }} rooms)
                                </option>
                            @endforeach
                        </select>

                    </div>
                    <div class="col-md-4">
                        <label>Status</label>
                        <select id="filterStatus" class="form-control">
                            <option value="">All statuses</option>
                            <option value="Available">Available</option>
                            <option value="Draft">Draft</option>
                            <option value="Published">Published</option>
                            <option value="Active">Active</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Start Date</label>
                        <input type="date" id="filterStartDate" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label>End Date</label>
                        <input type="date" id="filterEndDate" class="form-control">
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar -->
        <div class="card">
            <div class="card-body">
                <h4 class="mb-3">Trips Calendar</h4>
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.15/index.global.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let calendar;

function loadCalendar() {
    let calendarEl = document.getElementById('calendar');
    if(calendar) calendar.destroy();

    let resources = [
        @foreach($boats as $boat)
            @foreach($boat->rooms as $room)
                { id: 'room-{{ $room->id }}', title: '{{ $boat->name }} - {{ $room->room_name }}' },
            @endforeach
        @endforeach
    ];

    calendar = new FullCalendar.Calendar(calendarEl, {
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        initialView: 'resourceTimelineMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'resourceTimelineDay,resourceTimelineWeek,resourceTimelineMonth'
        },
        resources: resources,
        events: {
            url: "{{ route('trips.events') }}",
            method: 'GET',
        },
        height: 700,
        eventColor: '#378006',
        nowIndicator: true,
        resourceAreaHeaderContent: 'Rooms / Boats',
        eventClick: function(info) {
            if(info.event.title === 'Available') {
                Swal.fire('Slot is available!', '', 'info');
            } else {
                Swal.fire({
                    title: info.event.title,
                    html: `
                        <b>Start:</b> ${info.event.start.toISOString().split('T')[0]} <br>
                        <b>End:</b> ${info.event.end ? info.event.end.toISOString().split('T')[0] : '-'}
                    `,
                    icon: 'info'
                });
            }
        }
    });

    calendar.render();
}


function getFilters() {
    return {
        boat: $('#filterBoat').val(),
        status: $('#filterStatus').val(),
        start_date: $('#filterStartDate').val(),
        end_date: $('#filterEndDate').val()
    };
}

$('#filterBoat, #filterStatus, #filterStartDate, #filterEndDate').on('change', loadCalendar);

$(document).ready(loadCalendar);

// Copy widget code function
function copyWidgetCode(tripId) {
    const span = document.getElementById('widgetCode' + tripId);
    const text = span.innerText;

    const temp = document.createElement('textarea');
    temp.value = text;
    document.body.appendChild(temp);
    temp.select();
    document.execCommand('copy');
    document.body.removeChild(temp);

    Swal.fire({
        icon: 'success',
        title: 'Widget code copied!',
        showConfirmButton: false,
        timer: 1500
    });
}
</script>
@endsection
