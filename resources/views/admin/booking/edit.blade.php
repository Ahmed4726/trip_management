@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container mx-2 pt-3">

<h4>Edit Booking</h4>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

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

    {{-- Slot --}}
    <div class="row">
        <div class="col-md-6 mb-3">
            <label>Slot</label>
            <select id="slotSelect" name="slot_id" class="form-control">
                <option value="">-- Select Slot (or create inline) --</option>
                <option value="">Create inline Slot</option>
                @foreach($slots as $slot)
                    <option value="{{ $slot->id }}" {{ $booking->slot_id == $slot->id ? 'selected' : '' }}>
                        {{ $slot->boat->name }} | {{ $slot->start_date->format('d-m-Y') }} → {{ $slot->end_date->format('d-m-Y') }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Source --}}
        <div class="col-md-6 mb-3">
            <label>Source</label>
            <select name="source" id="sourceSelect" class="form-control">
                <option value="Direct" {{ $booking->source == 'Direct' ? 'selected' : '' }}>Direct</option>
                <option value="Agent" {{ $booking->source == 'Agent' ? 'selected' : '' }}>Agent</option>
            </select>
        </div>

        {{-- Agent --}}
        <div class="col-md-6 mb-3" id="agentWrapper">
            <label>Agent</label>
            <select name="agent_id" id="agentSelect" class="form-control" {{ $booking->source != 'Agent' ? 'disabled' : '' }}>
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
                <option value="">-- Select Salesperson --</option>
                @foreach($salespersons as $sp)
                    <option value="{{ $sp->id }}" {{ $booking->salesperson_id == $sp->id ? 'selected' : '' }}>{{ $sp->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row">
        {{-- Booking Status --}}
        <div class="col-md-6 mb-3">
            <label>Status</label>
            <select name="status" class="form-control" required>
                @foreach(['Pending','Available','Booked','Completed','Cancelled'] as $status)
                    <option value="{{ $status }}" {{ $booking->status == $status ? 'selected' : '' }}>{{ $status }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Inline Slot Creation --}}
    <div id="inlineSlotWrapper" class="border p-3 mb-3 {{ !$booking->slot_id ? '' : 'd-none' }}">
        <h5>Inline Slot Creation</h5>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label>Boat</label>
                <select name="boat_id" id="boatSelect" class="form-control">
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
                <input type="date" name="start_date" class="form-control" value="{{ $booking->start_date?->format('Y-m-d') }}">
            </div>
            <div class="col-md-4 mb-3">
                <label>End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ $booking->end_date?->format('Y-m-d') }}">
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label>Region</label>
                <select name="region_id" class="form-control">
                    <option value="">-- Select Region --</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->id }}" {{ $booking->region_id == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label>Embarkation Port</label>
                <select name="embarkation_port_id" class="form-control">
                    <option value="">-- Select Port --</option>
                    @foreach($ports as $port)
                        <option value="{{ $port->id }}" {{ $booking->embarkation_port_id == $port->id ? 'selected' : '' }}>{{ $port->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label>Disembarkation Port</label>
                <select name="disembarkation_port_id" class="form-control">
                    <option value="">-- Select Port --</option>
                    @foreach($ports as $port)
                        <option value="{{ $port->id }}" {{ $booking->disembarkation_port_id == $port->id ? 'selected' : '' }}>{{ $port->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Price / Currency --}}
    <div class="row">
        <div class="col-md-6 mb-3">
            <label>Price</label>
            <input type="number" name="price" step="0.01" class="form-control" value="{{ $booking->price }}" required>
        </div>
        <div class="col-md-6 mb-3">
            <label>Currency</label>
                <select name="currency" class="form-control" required>
                    @foreach($currencies as $curr)
                        <option value="{{ $curr->id }}" {{ $booking->currency == $curr->id ? 'selected' : '' }}>
                            {{ $curr->symbol }} - {{ $curr->name }}
                        </option>
                    @endforeach
                </select>

        </div>
    </div>

    {{-- Rooms --}}
    <div class="mb-3">
        <label class="fw-bold">Rooms</label>
        <div id="roomMessage" class="text-muted small mb-2">Select a slot or boat to see rooms.</div>
        <div id="roomWrapper" class="row g-2"></div>
    </div>

    {{-- Rate Plan / Payment / Cancellation --}}
    <div class="row">
        <div class="col-md-6 mb-3">
            <label>Rate Plan</label>
            <select name="rate_plan_id" class="form-control" required>
                @foreach($ratePlans as $plan)
                    <option value="{{ $plan->id }}" {{ $booking->rate_plan_id == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label>Payment Policy</label>
            <select name="payment_policy_id" class="form-control" required>
                @foreach($paymentPolicies as $policy)
                    <option value="{{ $policy->id }}" {{ $booking->payment_policy_id == $policy->id ? 'selected' : '' }}>{{ $policy->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label>Cancellation Policy</label>
            <select name="cancellation_policy_id" class="form-control" required>
                @foreach($cancellationPolicies as $policy)
                    <option value="{{ $policy->id }}" {{ $booking->cancellation_policy_id == $policy->id ? 'selected' : '' }}>{{ $policy->name }}</option>
                @endforeach
            </select>
        </div>
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

    {{-- Guest → Room Assignment --}}
    <div class="mb-3">
        <label class="fw-bold">Guest Room Assignment</label>
        <div id="guestRoomWrapper" class="row g-2 text-muted small">
            Select rooms and guests to assign.
        </div>
    </div>

    {{-- Notes --}}
    <div class="mb-3">
        <label>Notes</label>
        <textarea name="notes" class="form-control">{{ $booking->notes }}</textarea>
    </div>

    <button class="btn btn-success">Update Booking</button>
    <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">Cancel</a>
</form>
</div>
</div>

{{-- Add Guest Modal --}}
<div class="modal fade" id="guestModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="guestForm">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Add Guest</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">

          <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>

          <div class="mb-3">
            <label>Gender</label>
            <select name="gender" class="form-control" required>
              <option value="">-- Select Gender --</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
          </div>

          <div class="mb-3">
            <label>Date of Birth</label>
            <input type="date" name="dob" class="form-control" required>
          </div>

          <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control">
          </div>

          <div class="mb-3">
            <label>Passport</label>
            <input type="text" name="passport" class="form-control">
          </div>

          <div class="mb-3">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control">
          </div>

          <div class="mb-3">
            <label>Address</label>
            <input type="text" name="address" class="form-control">
          </div>

          <input type="hidden" name="company_id" value="{{ auth()->user()->company_id }}">

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save Guest</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<!-- Choices.js CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

<!-- Choices.js JS -->
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const slots = @json($slots);
    const boats = @json($boats);
    const booking = @json($booking);
    const bookingRoomIds = @json($bookingRoomIds ?? []);
    const guestRoomMapping = @json($guestRoomMapping ?? []);



    const sourceSelect      = document.getElementById('sourceSelect');
    const agentSelect       = document.getElementById('agentSelect');
    const slotSelect        = document.getElementById('slotSelect');
    const boatSelect        = document.getElementById('boatSelect');
    const inlineSlotWrapper = document.getElementById('inlineSlotWrapper');
    const roomWrapper       = document.getElementById('roomWrapper');
    const roomMessage       = document.getElementById('roomMessage');
    const addGuestBtn       = document.getElementById('addGuestBtn');
    const guestSelectEl     = document.getElementById('guestSelect');

    const guestChoices = new Choices(guestSelectEl, {
        removeItemButton: true,
        searchEnabled: true,
        shouldSort: false,
        placeholder: true,
        placeholderValue: 'Select guests',
    });

    function toggleAgentField() {
        agentSelect.disabled = sourceSelect.value !== 'Agent';
        if (agentSelect.disabled) agentSelect.value = '';
    }
    toggleAgentField();
    sourceSelect.addEventListener('change', toggleAgentField);

    function getMaxCapacity() {
        let total = 0;
        document.querySelectorAll('.room-check:checked').forEach(cb => {
            total += parseInt(cb.dataset.cap);
        });
        return total;
    }

    function showRoomMessage(show) {
        roomMessage.style.display = show ? 'block' : 'none';
    }

    function renderRooms(rooms) {
        roomWrapper.innerHTML = '';
        if (!rooms || !rooms.length) {
            showRoomMessage(true);
            return;
        }
        showRoomMessage(false);
    rooms.forEach(room => {
        const cap = parseInt(room.max_capacity ?? room.capacity ?? 0) + parseInt(room.extra_beds);
        const isChecked = bookingRoomIds.includes(room.id) ? 'checked' : '';

        roomWrapper.innerHTML += `
            <div class="col-md-4">
                <label class="card p-2 h-100">
                    <input type="checkbox"
                        class="form-check-input me-2 room-check"
                        data-cap="${cap}"
                        data-room-name="${room.room_name}"
                        value="${room.id}"
                        name="rooms[${room.id}]"
                        ${isChecked}>
                    <strong>${room.room_name}</strong><br>
                    <small class="text-muted">Max ${cap}</small>
                </label>
            </div>
        `;
    });

        bindRoomCapacity();
        enforceGuestLimit();
        buildGuestRoomMapping();
    }

function buildGuestRoomMapping() {
    const wrapper = document.getElementById('guestRoomWrapper');
    wrapper.innerHTML = '';
    const guests = guestChoices.getValue();
const rooms = [...document.querySelectorAll('.room-check:checked')];
    const allRooms = rooms.map(r => ({
        value: r.value,
        dataset: r.dataset
    }));
    if (!guests.length || !allRooms.length) {
        wrapper.innerHTML = '<div class="text-muted">Select rooms and guests to assign.</div>';
        return;
    }

guests.forEach(g => {
    // build options without trying to set selected
    let options = allRooms.map(r =>
        `<option value="${r.value}">${r.dataset.roomName}</option>`
    ).join('');

    wrapper.innerHTML += `
        <div class="col-md-6">
            <label>${g.label}</label>
            <select name="guest_rooms[${g.value}]" class="form-control guest-room-select" required>
                ${options}
            </select>
        </div>
    `;

    // set the selected value after inserting
    const selectEl = wrapper.querySelector(`select[name="guest_rooms[${g.value}]"]`);
    if (guestRoomMapping[g.value]) {
        selectEl.value = guestRoomMapping[g.value]; // select the assigned room
    }
});

}


    function enforceGuestLimit() {
        const maxCap = getMaxCapacity();
        const selected = guestChoices.getValue(true);
        if (maxCap === 0) {
            guestChoices.disable();
            addGuestBtn.style.display = 'none';
            return;
        }
        guestChoices.enable();
        if (selected.length > maxCap) {
            const allowed = selected.slice(0, maxCap);
            guestChoices.removeActiveItems();
            allowed.forEach(val => guestChoices.setChoiceByValue(val));
        }
        addGuestBtn.style.display = guestChoices.getValue(true).length >= maxCap ? 'none' : 'inline-block';
    }

    function bindRoomCapacity() {
        document.querySelectorAll('.room-check').forEach(cb => {
            cb.addEventListener('change', () => {
                enforceGuestLimit();
                buildGuestRoomMapping();
            });
        });
    }

    // Initial rendering
    if (booking.slot && booking.slot.boat?.rooms) {
        renderRooms(booking.slot.boat.rooms);
    } else if (booking.boat?.rooms) {
        renderRooms(booking.boat.rooms);
    } else {
        showRoomMessage(true);
    }

    // Preselect guests
    guestChoices.setChoices(
        booking.guests.map(g => ({ value: g.id, label: g.name, selected: true })),
        'value',
        'label',
        false
    );
    enforceGuestLimit();

    slotSelect.addEventListener('change', function () {
        const slot = slots.find(s => s.id == this.value);
        if (!slot) {
            inlineSlotWrapper.classList.remove('d-none');
            roomWrapper.innerHTML = '';
            showRoomMessage(true);
            enforceGuestLimit();
            return;
        }
        inlineSlotWrapper.classList.add('d-none');
        if (slot.boat?.rooms) renderRooms(slot.boat.rooms);
    });

    boatSelect.addEventListener('change', function () {
        const boat = boats.find(b => b.id == this.value);
        if (!boat?.rooms) {
            roomWrapper.innerHTML = '';
            showRoomMessage(true);
            enforceGuestLimit();
            return;
        }
        renderRooms(boat.rooms);
    });

    $('#guestForm').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);
        $.ajax({
            url: '{{ route("admin.guests.store") }}',
            method: 'POST',
            data: form.serialize(),
            success: function (guest) {
                guestChoices.setChoices(
                    [{ value: guest.id, label: guest.name, selected: true }],
                    'value',
                    'label',
                    false
                );
                $('#guestModal').modal('hide');
                form[0].reset();
                enforceGuestLimit();
            },
            error: function () {
                alert('Failed to create guest');
            }
        });
    });

});
</script>

@endsection
