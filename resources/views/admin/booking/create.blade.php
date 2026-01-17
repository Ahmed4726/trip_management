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

{{-- Slot --}}
<div class="row">
<div class="col-md-6 mb-3">
    <label>Slot</label>
    <select id="slotSelect" name="slot_id" class="form-control">
        <option value="">-- Select Slot (or create inline) --</option>
        @foreach($slots as $slot)
            <option value="{{ $slot->id }}">
                {{ $slot->boat->name }} | {{ $slot->start_date }} → {{ $slot->end_date }}
            </option>
        @endforeach
    </select>
</div>


{{-- Source --}}
<div class="col-md-6 mb-3">
    <label>Source</label>
    <select name="source" id="sourceSelect" class="form-control">
        <option value="Direct">Direct</option>
        <option value="Agent">Agent</option>
    </select>
</div>

{{-- Agent --}}
<div class="col-md-6 mb-3 d-none" id="agentWrapper">
    <label>Agent</label>
    <select name="agent_id" class="form-control">
        <option value="">-- Select Agent --</option>
        @foreach($agents as $agent)
            <option value="{{ $agent->id }}">{{ $agent->first_name }} {{ $agent->last_name }}</option>
        @endforeach
    </select>
</div>

    <div class="col-md-6 mb-3">
        <label>Salesperson</label>
        <select name="salesperson_id" class="form-control" required>
            <option value="">-- Select Salesperson --</option>
            @foreach($salespersons as $sp)
                <option value="{{ $sp->id }}">{{ $sp->name }}</option>
            @endforeach
        </select>
    </div>
</div>

{{-- Inline Slot Creation --}}
<div id="inlineSlotWrapper" class="border p-3 mb-3 d-none">
    <h5>Inline Slot Creation</h5>
    <div class="row">
    <div class="col-md-4 mb-3">
        <label>Boat</label>
        <select name="boat_id" class="form-control">
            <option value="">-- Select Boat --</option>
            @foreach($boats as $boat)
                <option value="{{ $boat->id }}">{{ $boat->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 mb-3">
        <label>Start Date</label>
        <input type="date" name="start_date" class="form-control">
    </div>
    <div class="col-md-4 mb-3">
        <label>End Date</label>
        <input type="date" name="end_date" class="form-control">
    </div>
    </div>
    <div class="row">
    <div class="col-md-4 mb-3">
        <label>Region</label>
        <select name="region_id" class="form-control">
            <option value="">-- Select Region --</option>
            @foreach($regions as $region)
                <option value="{{ $region->id }}">{{ $region->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 mb-3">
        <label>Embarkation Port</label>
        <select name="embarkation_port_id" class="form-control">
            <option value="">-- Select Port --</option>
            @foreach($ports as $port)
                <option value="{{ $port->id }}">{{ $port->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 mb-3">
        <label>Disembarkation Port</label>
        <select name="disembarkation_port_id" class="form-control">
            <option value="">-- Select Port --</option>
            @foreach($ports as $port)
                <option value="{{ $port->id }}">{{ $port->name }}</option>
            @endforeach
        </select>
    </div>
    </div>
</div>



{{-- Price / Currency / Salesperson --}}
<div class="row">
    <div class="col-md-6 mb-3">
        <label>Price</label>
        <input type="number" name="price" step="0.01" class="form-control" required>
    </div>

    <div class="col-md-6 mb-3">
        <label>Currency</label>
        <select name="currency" class="form-control" required>
            <option value="USD">USD – Dollar ($)</option>
            <option value="EUR">EUR – Euro (€)</option>
            <option value="IDR">IDR – Indonesian Rupiah (Rp)</option>
        </select>
    </div>
</div>

{{-- Rooms --}}
<div class="mb-3">
    <label>Rooms</label>
    <select name="rooms[]" id="roomSelect" class="form-control" multiple required></select>
</div>

{{-- Rate Plan --}}
<div class="row">
    <div class="col-md-6 mb-3">
        <label>Rate Plan</label>
        <select name="rate_plan_id" class="form-control" required>
            @foreach($ratePlans as $plan)
                <option value="{{ $plan->id }}">{{ $plan->name }}</option>
        @endforeach
    </select>
</div>

{{-- Payment Policy --}}
<div class="col-md-6 mb-3">
    <label>Payment Policy</label>
    <select name="payment_policy_id" class="form-control" required>
        @foreach($paymentPolicies as $policy)
            <option value="{{ $policy->id }}">{{ $policy->name }}</option>
        @endforeach
    </select>
</div>

{{-- Cancellation Policy --}}
<div class="col-md-6 mb-3">
    <label>Cancellation Policy</label>
    <select name="cancellation_policy_id" class="form-control" required>
        @foreach($cancellationPolicies as $policy)
            <option value="{{ $policy->id }}">{{ $policy->name }}</option>
        @endforeach
    </select>
</div>

{{-- Company --}}
@if (Auth::user()->hasRole('admin'))
    <div class="col-md-6 mb-3">
        <label>Company</label>
        <select name="company_id" class="form-control" required>
            @foreach($companies as $company)
                <option value="{{ $company->id }}">{{ $company->name }}</option>
            @endforeach
        </select>
    </div>
@endif
</div>

{{-- Guests --}}
<div class="mb-3">
    <label>Guests</label>
    <select name="guests[]" id="guestSelect" class="form-control" multiple>
        @foreach($guests as $guest)
            <option value="{{ $guest->id }}">{{ $guest->name }}</option>
        @endforeach
    </select>
</div>

<button type="button" id="addGuestBtn" class="btn btn-sm btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#guestModal">
    + Add Guest
</button>

{{-- Notes --}}
<div class="mb-3">
    <label>Notes</label>
    <textarea name="notes" class="form-control"></textarea>
</div>



<button class="btn btn-success">Create Booking</button>
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
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>


<script>
const slots = @json($slots);
let roomCaps = {};
let maxCap = 0;

// Populate room capacities
slots.forEach(s => {
    s.boat.rooms.forEach(r => {
        roomCaps[r.id] = r.capacity + r.extra_beds;
    });
});

// Toggle Agent field
const sourceSelect = document.getElementById('sourceSelect');
const agentWrapper = document.getElementById('agentWrapper');
sourceSelect.onchange = e => agentWrapper.classList.toggle('d-none', e.target.value !== 'Agent');

// Show/hide inline slot creation
const slotSelect = document.getElementById('slotSelect');
const inlineSlotWrapper = document.getElementById('inlineSlotWrapper');
slotSelect.onchange = function () {
    const slot = slots.find(s => s.id == this.value);
    inlineSlotWrapper.classList.toggle('d-none', !!slot);

    const roomSelect = document.getElementById('roomSelect');
    roomSelect.innerHTML = '';
    if (!slot) return;

    slot.boat.rooms.forEach(r => {
        roomSelect.innerHTML += `<option value="${r.id}">${r.room_name} (Max ${roomCaps[r.id]})</option>`;
    });
};

// Limit guest additions based on room capacity
const roomSelect = document.getElementById('roomSelect');
const guestSelect = document.getElementById('guestSelect');
const addGuestBtn = document.getElementById('addGuestBtn');

roomSelect.onchange = guestSelect.onchange = toggleGuestLimit;

function toggleGuestLimit() {
    maxCap = [...roomSelect.selectedOptions].reduce((t,o)=>t+roomCaps[o.value],0);
    addGuestBtn.style.display = guestSelect.selectedOptions.length >= maxCap ? 'none' : 'inline-block';
}

$('#guestForm').submit(function(e){
    e.preventDefault();
    let form = $(this);
    $.ajax({
        url: '{{ route("admin.guests.store") }}',
        type: 'POST',
        data: form.serialize(),
        success: function(guest){
            // Add new guest to multi-select
            $('#guestSelect').append(`<option value="${guest.id}" selected>${guest.name}</option>`);
            $('#guestModal').modal('hide');
            form[0].reset();
        },
        error: function(xhr){
            alert('Error: ' + (xhr.responseJSON?.message || 'Failed to create guest'));
        }
    });
});
</script>
@endsection
