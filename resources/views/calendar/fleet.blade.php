@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
<section class="content">
<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Fleet Calendar / List</h4>
</div>

<div class="d-flex mb-3 gap-3">
    {{-- Boat Filter --}}
    <div class="col-md-6">
        <select id="boatFilter" class="form-control">
            <option value="">All Boats</option>
            @foreach($boats as $boat)
                <option value="{{ $boat->id }}">{{ $boat->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- BUTTON VIEW TOGGLE --}}
    <div class="btn-group" role="group">
        <button type="button" id="btnCalendarView" class="btn btn-primary active mx-2">Calendar View</button>
        <button type="button" id="btnListView" class="btn btn-outline-primary">List View</button>
    </div>
</div>


{{-- CALENDAR VIEW --}}
<div id="calendarWrapper" class="card">
    <div class="card-body">
        <div id="calendar" style="height:500px;"></div>
    </div>
</div>

{{-- LIST VIEW --}}
<div id="listWrapper" class="card d-none mt-3">
    <div class="card-body">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Boat</th>
                    <th>Room</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="listTableBody">
                <tr>
                    <td colspan="7" class="text-center text-muted">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

</div>
</section>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const boatFilter = document.getElementById('boatFilter');
    const calendarEl = document.getElementById('calendar');
    const calendarWrapper = document.getElementById('calendarWrapper');
    const listWrapper = document.getElementById('listWrapper');
    const listTableBody = document.getElementById('listTableBody');

    const btnCalendarView = document.getElementById('btnCalendarView');
    const btnListView = document.getElementById('btnListView');

    let calendar;
    let lastEvents = [];

    /* ================= CALENDAR INIT ================= */
    calendar = window.initFleetCalendar(calendarEl, {
        schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
        initialView: 'resourceTimelineMonth',

        resources(fetchInfo, success, failure) {
            const boatId = boatFilter.value;
            fetch(`/api/calendar/fleet/resources?boat_id=${boatId}`)
                .then(r => r.json()).then(success).catch(failure);
        },

        events(fetchInfo, success, failure) {
            const boatId = boatFilter.value;
            fetch(`/api/calendar/fleet/events?boat_id=${boatId}`)
                .then(r => r.json())
                .then(data => {
                    lastEvents = data;
                    success(data);

                    // If List view active, render it
                    if(listWrapper.classList.contains('d-none') === false) renderListFromEvents(data);
                })
                .catch(failure);
        },

        resourceAreaHeaderContent: 'Fleet',
        nowIndicator: true,
        editable: false,

        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'resourceTimelineMonth,resourceTimelineYear'
        },

        eventClick(info) {
            const { type, slot_id, booking_id } = info.event.extendedProps;
            if (type === 'slot') location = `/admin/slots/${slot_id}/edit`;
            if (type === 'booking') location = `/admin/bookings/${booking_id}/edit`;
        },

        eventDidMount(info) {
            const { type, boat_name, room_name } = info.event.extendedProps;
            const title = room_name ? `${boat_name} | ${room_name}` : boat_name;
            info.el.setAttribute('title', `${title} (${type.toUpperCase()})`);
        }
    });

    /* ================= BUTTON VIEW TOGGLE ================= */
    btnCalendarView.addEventListener('click', () => {
        calendarWrapper.classList.remove('d-none');
        listWrapper.classList.add('d-none');

        btnCalendarView.classList.add('btn-primary', 'active');
        btnCalendarView.classList.remove('btn-outline-primary');
        btnListView.classList.remove('btn-primary', 'active');
        btnListView.classList.add('btn-outline-primary');

        calendar.render();
    });

    btnListView.addEventListener('click', () => {
        calendarWrapper.classList.add('d-none');
        listWrapper.classList.remove('d-none');

        btnListView.classList.add('btn-primary', 'active');
        btnListView.classList.remove('btn-outline-primary');
        btnCalendarView.classList.remove('btn-primary', 'active');
        btnCalendarView.classList.add('btn-outline-primary');

        renderListFromEvents(lastEvents);
    });

    /* ================= FILTER ================= */
    boatFilter.addEventListener('change', () => {
        calendar.refetchResources();
        calendar.refetchEvents();
    });

    /* ================= LIST VIEW RENDER ================= */
function renderListFromEvents(events) {
    if(!events.length) {
        listTableBody.innerHTML = `<tr><td colspan="7" class="text-center text-muted">No records found</td></tr>`;
        return;
    }

    listTableBody.innerHTML = '';
    events.forEach(e => {
        const type = e.extendedProps.type;
        const boatName = e.extendedProps.boat_name || '-';
        const roomName = e.extendedProps.room_name || '-';
        const startDate = e.start ? new Date(e.start).toLocaleDateString('en-GB') : '-';
        const endDate = e.end ? new Date(e.end).toLocaleDateString('en-GB') : '-';

        // Show "BOOKED" for bookings
        const displayType = type === 'booking' ? 'BOOKED' : type === 'slot' ? 'SLOT' : type.toUpperCase();
        const status = type === 'booking' ? 'Booked' : type === 'slot' ? e.extendedProps.status : 'Available';

        const editUrl = type === 'booking' ? `/admin/bookings/${e.extendedProps.booking_id}/edit` :
                        type === 'slot' ? `/admin/slots/${e.extendedProps.slot_id}/edit` : '#';

        listTableBody.innerHTML += `
            <tr>
                <td>${boatName}</td>
                <td>${roomName}</td>
                <td>${startDate}</td>
                <td>${endDate}</td>
                <td><span class="badge ${type==='booking'?'bg-danger':type==='slot'?'bg-success':'bg-primary'}">${displayType}</span></td>
                <td>${status}</td>
                <td><a href="${editUrl}" class="btn btn-sm btn-outline-primary">Edit</a></td>
            </tr>
        `;
    });
}


});
</script>
@endsection
