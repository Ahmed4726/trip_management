@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container pt-3">

<h4>Edit Booking #{{ $booking->id }}</h4>

<form method="POST" action="{{ route('admin.bookings.update', $booking) }}">
@csrf
@method('PUT')

{{-- Slot Dropdown --}}
<div class="mb-3">
    <label>Select Slot</label>
    <select id="slotSelect" name="slot_id" class="form-control">
        <option value="">-- Select Slot --</option>
        @foreach($slots as $slot)
            <option value="{{ $slot->id }}" data-rooms='@json($slot->boat->rooms)'
                {{ old('slot_id', $booking->slot_id) == $slot->id ? 'selected' : '' }}>
                Slot #{{ $slot->id }} - {{ $slot->boat->name }} ({{ $slot->start_date }} → {{ $slot->end_date }})
            </option>
        @endforeach
    </select>
</div>

{{-- Boat --}}
<div class="mb-3">
    <label>Boat</label>
    <select name="boat_id" id="boatSelect" class="form-control" required>
        @foreach($slots->pluck('boat')->unique('id') as $boat)
            @if($boat)
                <option value="{{ $boat->id }}" {{ old('boat_id', $booking->boat_id) == $boat->id ? 'selected' : '' }}>
                    {{ $boat->name }}
                </option>
            @endif
        @endforeach
    </select>
</div>

{{-- Room --}}
<div class="mb-3">
    <label>Room</label>
    <select name="room_id" id="roomSelect" class="form-control" required>
        @foreach($booking->boat->rooms as $room)
            <option value="{{ $room->id }}" {{ old('room_id', $booking->room_id) == $room->id ? 'selected' : '' }}>
                {{ $room->room_name }}
            </option>
        @endforeach
    </select>
</div>

{{-- Guest Info --}}
<div class="mb-3">
    <label>Guest Name</label>
    <input type="text" name="guest_name" class="form-control" value="{{ old('guest_name', $booking->guest_name) }}" required>
</div>
<div class="mb-3">
    <label>Guest Email</label>
    <input type="email" name="guest_email" class="form-control" value="{{ old('guest_email', $booking->guest_email) }}">
</div>
<div class="mb-3">
    <label>Guest Phone</label>
    <input type="text" name="guest_phone" class="form-control" value="{{ old('guest_phone', $booking->guest_phone) }}">
</div>
<div class="mb-3">
    <label>Notes</label>
    <textarea name="notes" class="form-control">{{ old('notes', $booking->notes) }}</textarea>
</div>

{{-- Status --}}
<div class="mb-3">
    <label>Status</label>
    <select name="status" class="form-control" required>
        @foreach(['Pending','Confirmed','Cancelled'] as $status)
            <option value="{{ $status }}" {{ old('status', $booking->status)==$status?'selected':'' }}>{{ $status }}</option>
        @endforeach
    </select>
</div>

<button class="btn btn-success">Update Booking</button>
<a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">Cancel</a>
</form>

</div>
</div>

<script>
const slots = @json($slots);

function populateRooms(slotId, roomSelect, selectedRoomId = null) {
    roomSelect.innerHTML = '';
    if (!slotId) return;
    const slot = slots.find(s => s.id === parseInt(slotId));
    slot.boat.rooms.forEach(r => {
        const opt = document.createElement('option');
        opt.value = r.id;
        opt.textContent = r.name;
        if(selectedRoomId && selectedRoomId==r.id) opt.selected = true;
        roomSelect.appendChild(opt);
    });
}

document.getElementById('slotSelect').addEventListener('change', function() {
    const roomSelect = document.getElementById('roomSelect');
    populateRooms(this.value, roomSelect);
});
</script>
@endsection
