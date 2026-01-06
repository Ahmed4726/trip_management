@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
    <div class="container pt-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">Manage Slots</h2>
            @can('create-trip')
            <a href="{{ route('trips.create') }}" class="btn btn-primary">Create</a>
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
                                <option value="{{ $boat->name }}">
                                    {{ $boat->name }} ({{ $boat->rooms_count }} rooms)
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
                <h4 class="mb-3">Slots Calendar</h4>
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
let boats = @json($boats->map(fn($b) => [
    'id' => 'boat-' . $b->id,
    'title' => $b->name . ' (' . $b->rooms_count . ' rooms)'
]));
</script>

<script>
let calendar;

function loadCalendar() {
    let calendarEl = document.getElementById('calendar');
    if (calendar) calendar.destroy();

    // Boat filter
    let selectedBoat = $('#filterBoat').val();

    // All boats by default
    let allResources = boats; // replace the old hardcoded array

    // If a boat is selected in the filter, only show that one
    let resources = selectedBoat
        ? allResources.filter(r => r.id === 'boat-' + selectedBoat)
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
                title: "Create a new slot?",
                text: "Do you want to create a slot starting on " + info.dateStr + "?",
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
        // alert(props.start_date);

    Swal.fire({
        title: 'Edit Slot',
        width: 600,
        html: `
            <form id="editTripForm" class="text-left">
                <div class="form-group mb-2">
                    <label>Title</label>
                    <input type="text" id="tripTitle" class="form-control" value="${info.event.title}">
                    <input type="hidden" id="tripType" class="form-control" value="${info.event.trip_type}">

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
                        <option ${props.status === 'On Hold' ? 'selected' : ''}>On Hold</option>
                        <option ${props.status === 'Booked' ? 'selected' : ''}>Booked</option>
                        <option ${props.status === 'Crossing' ? 'selected' : ''}>Crossing</option>
                        <option ${props.status === 'Maintenance' ? 'selected' : ''}>Maintenance</option>
                        <option ${props.status === 'Docking' ? 'selected' : ''}>Docking</option>
                    </select>
                </div>
                <div class="form-row mb-2 d-flex">
                    <div class="form-group flex-fill me-2">
                        <label>Start Date</label>
                        <input type="date" id="tripStart" class="form-control" value="${info.event.startStr}">
                    </div>
                    <div class="form-group flex-fill">
                        <label>End Date</label>
                        <input type="date" id="tripEnd" class="form-control" value="${info.event.endStr}">
                    </div>
                </div>
                <div class="form-group mb-2">
                    <label>Notes</label>
                    <textarea id="tripNotes" class="form-control" rows="3">${props.notes || ''}</textarea>
                </div>

                <!-- Booking Widget Button -->
                <div class="mt-3 text-center">
                    <button type="button" class="btn btn-info" 
                        onclick="openBookingWidget('${props.trip_id}', '${props.boat}', '${props.company}')">
                        View Booking Widget
                    </button>

                </div>
            </form>
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
                    Swal.fire("Saved!", "Slot updated successfully.", "success");
                    calendar.refetchEvents();
                }
            });
        } else if (result.isDenied) {
            Swal.fire({
                title: "Are you sure?",
                text: "This will permanently delete the slot.",
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
                            Swal.fire("Deleted!", "Slot removed.", "success");
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


function openBookingWidget(tripId, boat, company) {
    let companyName = company && company !== "" ? company : "{{ auth()->user()->company->name ?? 'SAMARA' }}";
    let boatSlug = boat ? boat.replace(/\s+/g, '-') : 'default-boat';

    let url = `http://trip_management.test/public/widget?company=${companyName}&boat=${boatSlug}&trip=${tripId}`;

    Swal.fire({
        title: "Booking Widget",
        html: `<iframe src="${url}" style="width:100%;height:600px;border:0;"></iframe>`,
        width: 800,
        showConfirmButton: false,
        showCloseButton: true
    });
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
</script>
@endsection
