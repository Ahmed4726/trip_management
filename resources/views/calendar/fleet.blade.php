@extends(isset($iframe) ? 'layouts.app' : 'layouts.admin')

@section('content')
<div class="content-wrapper">
<section class="content">
<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Fleet Calendar</h4>

    {{-- @if(!isset($iframe))
    <button id="copyEmbed" class="btn btn-sm btn-outline-primary">
        Copy Embed Code
    </button>
    @endif --}}
</div>

<div class="card">
<div class="card-body">
    <div id="calendar" style="height:860px;"></div>
</div>
</div>

</div>
</section>
</div>

{{-- ================= STYLES ================= --}}
<style>
/* OPEN TRIP – visually distinct */
.fc-event.open-trip {
    border: 2px dashed rgba(255,255,255,.85);
    font-weight: 600;
    height: 36px;
}

.fc-event.open-trip .fc-event-title {
    text-transform: uppercase;
    letter-spacing: .4px;
}

/* Available room background */
.fc-bg-event {
    opacity: .6;
}

/* Improve row spacing */
.fc-timeline-lane-frame {
    padding-top: 4px;
    padding-bottom: 4px;
}
</style>

{{-- ================= SCRIPT ================= --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const isIframe = {{ isset($iframe) ? 'true' : 'false' }};
    const el = document.getElementById('calendar');

    window.initFleetCalendar(el, {

        schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',

        initialView: 'resourceTimelineMonth',

        resources: '/api/calendar/fleet/resources',
        events: '/api/calendar/fleet/events',

        resourceAreaHeaderContent: 'Fleet',
        nowIndicator: true,

        editable: !isIframe,
        selectable: false,

        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'resourceTimelineMonth,resourceTimelineYear'
        },

eventClick(info) {
    if (isIframe) return;

    const { type, slot_id, booking_id, room_id } = info.event.extendedProps;

    /* SLOT ROW CLICK */
    if (type === 'slot') {
        window.location = `/admin/slots/${slot_id}/edit`;
        return;
    }

    /* BOOKED ROOM CLICK */
    if (type === 'booking') {
        window.location = `/admin/bookings/${booking_id}/edit`;
        return;
    }

    /* AVAILABLE ROOM CLICK */
    if (type === 'available') {
        window.location = `/admin/bookings/create?slot_id=${slot_id}&room_id=${room_id}`;
        return;
    }
},


        eventDidMount(info) {
            const { type, booking_count, capacity } = info.event.extendedProps;

            /* Tooltip for Open Trips */
            if (type === 'open') {
                info.el.title =
                    `Open Trip\nBookings: ${booking_count}/${capacity}`;
            }

            /* Tooltip for private trip rooms */
            if (type === 'booking') {
                info.el.title = `Booked Room`;
            }

            if (type === 'available') {
                info.el.title = `Available Room`;
            }
        }
    });

    /* COPY EMBED CODE */
    const btn = document.getElementById('copyEmbed');
    if (btn) {
        btn.addEventListener('click', () => {
            const code =
`<iframe
    src="${location.origin}/embed/fleet"
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
