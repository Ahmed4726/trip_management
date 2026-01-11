
@extends(isset($iframe) ? 'layouts.app' : 'layouts.admin')

@section('content')
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>{{ $boat->name }} – Availability & Bookings</h4>
                @if(!isset($iframe))
                <div>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back to Fleet</a>
                    <button id="copyEmbed" class="btn btn-sm btn-outline-primary">Copy Embed Code</button>
                </div>
                @endif
            </div>

            {{-- Statistics --}}
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center p-3">
                        <h6>Nights Booked</h6>
                        <span class="h4">{{ $nightsBooked }}</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center p-3">
                        <h6>Occupancy Rate</h6>
                        <span class="h4">{{ $occupancyRate }}%</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center p-3">
                        <h6>Direct vs Agent</h6>
                        <span class="h4">{{ $directCount }} / {{ $agentCount }}</span>
                    </div>
                </div>
                @if(auth()->user()->hasRole('admin'))
                <div class="col-md-3">
                    <div class="card text-center p-3">
                        <h6>Revenue</h6>
                        <span class="h4">${{ number_format($revenue,2) }}</span>
                    </div>
                </div>
                @endif
            </div>

            {{-- Calendar --}}
            <div class="card mb-4">
                <div class="card-body">
                    <div id="boatCalendar" style="height:600px;"></div>
                </div>
            </div>

            {{-- Booking Table --}}
            <div class="card">
                <div class="card-body">
                    <h5>Bookings</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Guest Name</th>
                                <th>Room</th>
                                <th>Slot</th>
                                <th>Source</th>
                                <th>Status</th>
                                <th>Price</th>
                                <th>Start / End</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $b)
                            <tr>
                                <td>{{ $b->customer_name }}</td>
                                <td>{{ $b->rooms['room_name'] }}</td>
                                <td>{{ $b->trip->title ?? '-' }}</td>
                                <td>{{ $b->source }}</td>
                                <td>{{ ucfirst($b->booking_status) }}</td>
                                <td>${{ $b->price }}</td>
                                <td>{{ $b->trip->start_date ?? '-' }} / {{ $b->trip->end_date ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
</div>

<style>
.fc-event.open-trip {
    border: 2px dashed rgba(255,255,255,.85);
    font-weight: 600;
}
.fc-bg-event { opacity: .6; }
.fc-timeline-lane-frame { padding: 4px 0; }
</style>


<link href="https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.15/index.global.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.15/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('boatCalendar');

    const calendar = new FullCalendar.Calendar(el, {
        schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
        initialView: 'resourceTimelineMonth',
        resourceAreaWidth: '250px',
        resourceAreaHeaderContent: 'Rooms & Boat',

        resources: '/api/calendar/boat/{{ $boat->id }}/resources',
        events: '/api/calendar/boat/{{ $boat->id }}/events',

        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'resourceTimelineMonth,listMonth'
        },

        nowIndicator: true,
        editable: true, // change to false if iframe
        selectable: false,

        eventClick(info) {
            const { type, trip_id, booking_id, room_id } = info.event.extendedProps;

            if (type === 'booking') {
                window.location = `/booking/edit/${booking_id}`;
            } else if (type === 'available') {
                window.location = `/create-booking?trip_id=${trip_id}&room_id=${room_id}`;
            } else {
                window.location = `/trips`;
            }
        },

        eventDidMount(info) {
            const { type, booking_count, capacity, room_name, title,customer_name } = info.event.extendedProps;

            if(type == 'booking'){
                info.el.innerHTML = `<strong>${customer_name}</strong><br>`;
                info.el.style.backgroundColor = '#4caf50'; // green booked
        } else if(type === 'open-trip'){
            const { booking_count, capacity } = info.event.extendedProps;
            info.el.innerHTML = `${info.event.title} (${booking_count}/${capacity})`;
            info.el.style.border = '2px dashed rgba(255,255,255,.85)';
            info.el.style.backgroundColor = info.event.backgroundColor || info.event.color;
        } else if(type === 'available'){
                        info.el.style.backgroundColor = '#ff9800'; // orange available
                    }
        }
    });

    calendar.render();

    // COPY EMBED BUTTON
    const btn = document.getElementById('copyEmbed');
    if (btn) {
        btn.addEventListener('click', () => {
            const code =
`<iframe
src="${location.origin}/embed/boat/{{ $boat->id }}"
width="100%"
height="900"
style="border:0"
loading="lazy">
</iframe>`;
            navigator.clipboard.writeText(code);
            btn.innerText = 'Copied!';
            setTimeout(() => btn.innerText = 'Copy Embed Code', 2000);
        });
    }
});


</script>
@endsection

