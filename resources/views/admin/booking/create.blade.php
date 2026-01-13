@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container pt-3">

<h4>Create Booking</h4>


        @foreach (['success','error'] as $msg)
            @if(session($msg))
                <div class="alert alert-{{ $msg == 'success' ? 'success' : 'danger' }}">
                    {{ session($msg) }}
                </div>
            @endif
        @endforeach
<form method="POST" action="{{ route('admin.bookings.store') }}">
@csrf

{{-- Slot Dropdown --}}
<div class="mb-3">
    <label>Select Slot (optional)</label>
    <select id="slotSelect" name="slot_id" class="form-control">
        <option value="">-- Select Slot (auto-create if none) --</option>
        @foreach($slots as $slot)
            <option value="{{ $slot->id }}" data-rooms='@json($slot->boat->rooms)'>
                Slot #{{ $slot->id }} - {{ $slot->boat->room_name }} ({{ $slot->start_date }} → {{ $slot->end_date }})
            </option>
        @endforeach
    </select>
</div>

{{-- Boat --}}
<div class="mb-3">
    <label>Boat</label>
    <select name="boat_id" id="boatSelect" class="form-control" required>
        <option value="">-- Select Boat --</option>
        @foreach($slots->pluck('boat')->unique('id') as $boat)
            @if($boat)
                <option value="{{ $boat->id }}">{{ $boat->name }}</option>
            @endif
        @endforeach
    </select>
</div>

{{-- Room --}}
<div class="mb-3">
    <label>Room</label>
    <select name="room_id" id="roomSelect" class="form-control" required>
        <option value="">-- Select Room --</option>
    </select>
</div>

{{-- Guest Info --}}
<div class="mb-3">
    <label>Guest Name</label>
    <input type="text" name="guest_name" class="form-control" required>
</div>
<div class="mb-3">
    <label>Guest Email</label>
    <input type="email" name="guest_email" class="form-control">
</div>
<div class="mb-3">
    <label>Guest Phone</label>
    <input type="text" name="guest_phone" class="form-control">
</div>
<div class="mb-3">
    <label>Notes</label>
    <textarea name="notes" class="form-control"></textarea>
</div>

<button class="btn btn-success">Create Booking</button>
<a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">Cancel</a>
</form>

</div>
</div>

<script>
const slots = @json($slots);

document.getElementById('slotSelect').addEventListener('change', function() {
    const slotId = parseInt(this.value);
    const roomSelect = document.getElementById('roomSelect');
    roomSelect.innerHTML = '';
    if (!slotId) return;
    const slot = slots.find(s => s.id === slotId);
    slot.boat.rooms.forEach(r => {
        const opt = document.createElement('option');
        opt.value = r.id;
        opt.textContent = r.room_name;
        roomSelect.appendChild(opt);
    });
});
</script>
@endsection
