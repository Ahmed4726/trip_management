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
                            <option value="Samara 1 (5 rooms)">Samara 1 (5 rooms)</option>
                            <option value="Samara 1 (4 rooms)">Samara 1 (4 rooms)</option>
                            <option value="Mischief (5 rooms)">Mischief (5 rooms)</option>
                            <option value="Samara (6 rooms)">Samara (6 rooms)</option>
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
    if (calendar) calendar.destroy();

    let selectedBoat = $('#filterBoat').val();

    let allResources = [
        { id: 'boat-1', title: 'Samara 1 (5 rooms)' },
        { id: 'boat-2', title: 'Samara 1 (4 rooms)' },
        { id: 'boat-3', title: 'Mischief (5 rooms)' },
        { id: 'boat-4', title: 'Samara (6 rooms)' }
    ];

    let resources = selectedBoat
        ? allResources.filter(r => r.title === selectedBoat)
        : allResources;

    calendar = new FullCalendar.Calendar(calendarEl, {
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        initialView: 'resourceTimelineMonth',
        height: 650,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'resourceTimelineDay,resourceTimelineWeek,resourceTimelineMonth'
        },
        resourceAreaHeaderContent: 'Boats',
        resources: resources,
        events: {
            url: "{{ route('trips.events') }}",
            method: 'GET',
            extraParams: getFilters
        },
        dateClick: function(info) {
            Swal.fire({
                title: "Create a new trip?",
                text: "Do you want to create a trip starting on " + info.dateStr + "?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Yes, create",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('trips.create') }}?start_date=" + info.dateStr + (selectedBoat ? "&boat=" + selectedBoat : "");
                }
            });
        },
        eventClick: function(info) {
            let props = info.event.extendedProps;

            Swal.fire({
                title: 'Trip Actions',
                width: 600,
                html: `
                    <form id="editTripForm" class="text-left mb-3">
                        <div class="form-group mb-2">
                            <label>Title</label>
                            <input type="text" id="tripTitle" class="form-control" value="${info.event.title}">
                        </div>
                        <div class="form-group mb-2">
                            <label>Guests</label>
                            <input type="number" id="tripGuests" class="form-control" value="${props.guests}">
                        </div>
                        <div class="form-group mb-2">
                            <label>Price</label>
                            <input type="number" id="tripPrice" class="form-control" value="${props.price}">
                        </div>
                        <div class="form-group mb-2">
                            <label>Region</label>
                            <input type="text" id="tripRegion" class="form-control" value="${props.region}">
                        </div>
                        <div class="form-group mb-2">
                            <label>Status</label>
                            <select id="tripStatus" class="form-control">
                                <option ${props.status === 'Available' ? 'selected' : ''}>Available</option>
                                <option ${props.status === 'Draft' ? 'selected' : ''}>Draft</option>
                                <option ${props.status === 'Published' ? 'selected' : ''}>Published</option>
                                <option ${props.status === 'Active' ? 'selected' : ''}>Active</option>
                                <option ${props.status === 'Completed' ? 'selected' : ''}>Completed</option>
                                <option ${props.status === 'Cancelled' ? 'selected' : ''}>Cancelled</option>
                            </select>
                        </div>
                        <div class="form-row mb-2 d-flex">
                            <div class="form-group flex-fill me-2">
                                <label>Start Date</label>
                                <input type="date" id="tripStart" class="form-control" value="${props.start}">
                            </div>
                            <div class="form-group flex-fill">
                                <label>End Date</label>
                                <input type="date" id="tripEnd" class="form-control" value="${props.end}">
                            </div>
                        </div>
                        <div class="form-group mb-2">
                            <label>Notes</label>
                            <textarea id="tripNotes" class="form-control" rows="3">${props.notes || ''}</textarea>
                        </div>
                    </form>

                    <button class="btn btn-outline-primary w-100" onclick="copyWidgetCode(${props.id})">
                        Copy Widget Code
                    </button>
                    <span id="widgetCode${props.id}" class="d-none">
<iframe src="${window.location.origin}/widget/booking?trip_id=${props.id}" 
        style="width:100%;height:600px;border:none;">
</iframe>
                    </span>
                `,
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: "Save",
                cancelButtonText: "Close",
                denyButtonText: "Delete",
                focusConfirm: false,
                preConfirm: () => {
                    return {
                        id: props.id,
                        title: document.getElementById('tripTitle').value,
                        guests: document.getElementById('tripGuests').value,
                        price: document.getElementById('tripPrice').value,
                        region: document.getElementById('tripRegion').value,
                        status: document.getElementById('tripStatus').value,
                        start_date: document.getElementById('tripStart').value,
                        end_date: document.getElementById('tripEnd').value,
                        notes: document.getElementById('tripNotes').value,
                        _token: "{{ csrf_token() }}"
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/trips/" + props.t_id,
                        type: "POST",
                        data: result.value,
                        success: function() {
                            Swal.fire("Saved!", "Trip updated successfully.", "success");
                            calendar.refetchEvents();
                        }
                    });
                } else if (result.isDenied) {
                    Swal.fire({
                        title: "Are you sure?",
                        text: "This will permanently delete the trip.",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Yes, delete"
                    }).then((res) => {
                        if (res.isConfirmed) {
                            $.ajax({
                                url: "/trips/" + props.id,
                                type: "DELETE",
                                data: { _token: "{{ csrf_token() }}" },
                                success: function() {
                                    Swal.fire("Deleted!", "Trip removed.", "success");
                                    calendar.refetchEvents();
                                }
                            });
                        }
                    });
                }
            });
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
