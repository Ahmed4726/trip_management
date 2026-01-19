@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
<div class="container pt-3">

<h4>Edit Booking</h4>

@foreach (['success','error'] as $msg)
    @if(session($msg))
        <div class="alert alert-{{ $msg == 'success' ? 'success' : 'danger' }}">
            {{ session($msg) }}
        </div>
    @endif
@endforeach

<form method="POST" action="{{ route('admin.bookings.update', $booking->id) }}">
@csrf
@method('PUT')

{{-- SLOT / SOURCE / AGENT / SALESPERSON --}}
<div class="row">

<div class="col-md-6 mb-3">
    <label>Slot</label>
    <select id="slotSelect" name="slot_id" class="form-control">
        <option value="">-- Select Slot (or create inline) --</option>
        @foreach($slots as $slot)
            <option value="{{ $slot->id }}" {{ $booking->slot_id == $slot->id ? 'selected' : '' }}>
                {{ $slot->boat->name }} | {{ $slot->start_date }} → {{ $slot->end_date }}
            </option>
        @endforeach
    </select>
</div>

<div class="col-md-6 mb-3">
    <label>Source</label>
    <select name="source" id="sourceSelect" class="form-control">
        <option value="Direct" {{ $booking->source == 'Direct' ? 'selected' : '' }}>Direct</option>
        <option value="Agent" {{ $booking->source == 'Agent' ? 'selected' : '' }}>Agent</option>
    </select>
</div>

<div class="col-md-6 mb-3">
    <label>Agent</label>
    <select name="agent_id" id="agentSelect" class="form-control">
        <option value="">-- Select Agent --</option>
        @foreach($agents as $agent)
            <option value="{{ $agent->id }}" {{ $booking->agent_id == $agent->id ? 'selected' : '' }}>
                {{ $agent->first_name }} {{ $agent->last_name }}
            </option>
        @endforeach
    </select>
</div>

<div class="col-md-6 mb-3">
    <label>Salesperson</label>
    <select name="salesperson_id" class="form-control" required>
        @foreach($salespersons as $sp)
            <option value="{{ $sp->id }}" {{ $booking->salesperson_id == $sp->id ? 'selected' : '' }}>
                {{ $sp->name }}
            </option>
        @endforeach
    </select>
</div>

</div>

{{-- STATUS --}}
<div class="row">
<div class="col-md-6 mb-3">
    <label>Status</label>
    <select name="status" class="form-control">
        @foreach(['Pending','Available','Booked','Completed','Cancelled'] as $st)
            <option value="{{ $st }}" {{ $booking->status == $st ? 'selected' : '' }}>
                {{ $st }}
            </option>
        @endforeach
    </select>
</div>
</div>

{{-- INLINE SLOT --}}
<div id="inlineSlotWrapper" class="border p-3 mb-3 {{ $booking->slot_id ? 'd-none' : '' }}">
<h5>Inline Slot Creation</h5>

<div class="row">
<div class="col-md-4 mb-3">
    <label>Boat</label>
    <select name="boat_id" class="form-control">
        <option value="">-- Select Boat --</option>
        @foreach($boats as $boat)
            <option value="{{ $boat->id }}" {{ $booking->boat_id == $boat->id ? 'selected' : '' }}>
                {{ $boat->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="col-md-4 mb-3">
    <label>Start Date</label>
    <input type="date" name="start_date" class="form-control"
           value="{{ optional($booking->slot)->start_date?->format('Y-m-d') }}">
</div>

<div class="col-md-4 mb-3">
    <label>End Date</label>
    <input type="date" name="end_date" class="form-control"
           value="{{ optional($booking->slot)->end_date?->format('Y-m-d') }}">
</div>
</div>

<div class="row">
<div class="col-md-4 mb-3">
    <label>Region</label>
    <select name="region_id" class="form-control">
        @foreach($regions as $region)
            <option value="{{ $region->id }}" {{ optional($booking->slot)->region_id == $region->id ? 'selected' : '' }}>
                {{ $region->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="col-md-4 mb-3">
    <label>Embarkation Port</label>
    <select name="embarkation_port_id" class="form-control">
        @foreach($ports as $port)
            <option value="{{ $port->id }}" {{ optional($booking->slot)->embarkation_port_id == $port->id ? 'selected' : '' }}>
                {{ $port->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="col-md-4 mb-3">
    <label>Disembarkation Port</label>
    <select name="disembarkation_port_id" class="form-control">
        @foreach($ports as $port)
            <option value="{{ $port->id }}" {{ optional($booking->slot)->disembarkation_port_id == $port->id ? 'selected' : '' }}>
                {{ $port->name }}
            </option>
        @endforeach
    </select>
</div>
</div>
</div>

{{-- PRICE --}}
<div class="row">
<div class="col-md-6 mb-3">
    <label>Price</label>
    <input type="number" name="price" class="form-control" value="{{ $booking->price }}" required>
</div>

<div class="col-md-6 mb-3">
    <label>Currency</label>
    <select name="currency" class="form-control">
        @foreach(['USD','EUR','IDR'] as $cur)
            <option value="{{ $cur }}" {{ $booking->currency == $cur ? 'selected' : '' }}>
                {{ $cur }}
            </option>
        @endforeach
    </select>
</div>
</div>

{{-- ROOMS --}}
<div class="mb-3">
<label>Rooms</label>
<select name="rooms[]" id="roomSelect" class="form-control" multiple>
    @foreach($booking->boat->rooms as $room)
        <option value="{{ $room->id }}" selected>
            {{ $room->room_name }} (Max {{ $room->capacity + $room->extra_beds }})
        </option>
    @endforeach
</select>
</div>

{{-- GUESTS --}}
<div class="mb-3">
    <label>Guests</label>
    <select name="guests[]" id="guestSelect" class="form-control" multiple>
        @php
            // If guests is a collection, pluck IDs; if string, explode to array
            $selectedGuests = $booking->guests instanceof \Illuminate\Support\Collection
                ? $booking->guests->pluck('id')->toArray()
                : (is_string($booking->guests) ? explode(',', $booking->guests) : []);
        @endphp

        @foreach($guests as $guest)
            <option value="{{ $guest->id }}" {{ in_array($guest->id, $selectedGuests) ? 'selected' : '' }}>
                {{ $guest->name }}
            </option>
        @endforeach
    </select>
</div>

<button type="button" id="addGuestBtn" class="btn btn-sm btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#guestModal">
+ Add Guest
</button>

{{-- NOTES --}}
<div class="mb-3">
<label>Notes</label>
<textarea name="notes" class="form-control">{{ $booking->notes }}</textarea>
</div>

<button class="btn btn-success">Update Booking</button>
<a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">Cancel</a>

</form>
</div>
</div>

{{-- CHOICES --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

const slots = @json($slots);
const roomCaps = {};
slots.forEach(s => s.boat.rooms.forEach(r => roomCaps[r.id] = r.capacity + r.extra_beds));

const roomChoices = new Choices('#roomSelect',{removeItemButton:true});
const guestChoices = new Choices('#guestSelect',{removeItemButton:true});

const sourceSelect = document.getElementById('sourceSelect');
const agentSelect = document.getElementById('agentSelect');
const slotSelect = document.getElementById('slotSelect');
const inlineSlotWrapper = document.getElementById('inlineSlotWrapper');
const addGuestBtn = document.getElementById('addGuestBtn');

function toggleAgent(){
    agentSelect.disabled = sourceSelect.value !== 'Agent';
}
toggleAgent();
sourceSelect.addEventListener('change',toggleAgent);

slotSelect.addEventListener('change',()=>{
    inlineSlotWrapper.classList.toggle('d-none',!!slotSelect.value);
});

function enforceCapacity(){
    let max = roomChoices.getValue(true).reduce((t,id)=>t+(roomCaps[id]||0),0);
    let guests = guestChoices.getValue(true);
    if(guests.length>max){
        guestChoices.removeActiveItems();
        guests.slice(0,max).forEach(g=>guestChoices.setChoiceByValue(g));
    }
    addGuestBtn.style.display = guests.length>=max ? 'none':'inline-block';
}
document.getElementById('roomSelect').addEventListener('change',enforceCapacity);
document.getElementById('guestSelect').addEventListener('change',enforceCapacity);
enforceCapacity();

});
</script>
@endsection
