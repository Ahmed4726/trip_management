@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4">Create Booking</h2>
        <a href="{{ route('bookings.index') }}" class="btn btn-secondary">Back</a>
    </div>

    @if(session('success'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '{{ session('success') }}',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
    </script>
    @endif

    <div class="card">
        <div class="card-body">
<form action="{{ route('bookings.store') }}" method="POST">
    @csrf
    {{-- Inline Trip Checkbox --}}
    <div class="form-check mb-3">
        <input type="checkbox" name="inline_trip" id="inlineTripCheckbox" class="form-check-input">
        <label for="inlineTripCheckbox" class="form-check-label">Create New Trip Inline</label>
    </div>

    {{-- Inline Trip Fields --}}
    <div id="inlineTripFields" style="display:none; border:1px solid #ccc; padding:15px; border-radius:5px;">
        <div class="row">
            <div class="col-md-4">
                <label>Trip Title</label>
                <input type="text" name="trip_title" class="form-control">
            </div>
            <div class="col-md-4">
                <label>Boat</label>
                <select name="boat" id="boat" class="form-control">
                    <option value="">Select boat</option>
                    @foreach($boats as $boat)
                        <option value="{{ $boat->id }}">{{ $boat->name }} ({{ $boat->rooms_count ?? $boat->rooms->count() }} rooms)</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Trip Type</label>
                <select name="trip_type" id="trip_type" class="form-control">
                    <option value="open">Open Trip</option>
                    <option value="private">Private Charter</option>
                </select>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-6">
                <label>Start Date</label>
                <input type="date" name="start_date" class="form-control" id="start_date">
            </div>
            <div class="col-md-6">
                <label>End Date</label>
                <input type="date" name="end_date" class="form-control" id="end_date">
            </div>
        </div>

        <div class="row mt-2" id="inlineGuestsRow" style="display:none;">
            <div class="col-md-6">
                <label>No Of Guests(Rooms)</label>
                <select name="inline_guests" id="inline_guests" class="form-control">
                    <option value="">Select a room</option>
                </select>
            </div>
            <div class="col-md-6">
                <label>Price</label>
                <input type="number" name="price" class="form-control" id="price" readonly>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-6">
                <label>Region</label>
                <input type="text" name="region" class="form-control" id="region">
            </div>
        </div>
    </div>

    {{-- Existing Trip Selection --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="trip_id">Select Trip</label>
            <select name="trip_id" id="trip_id" class="form-control" required>
                <option value="">Choose a trip</option>
                @foreach($trips as $trip)
                    <option value="{{ $trip->id }}">{{ $trip->title }} ({{ $trip->start_date }} - {{ $trip->end_date }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label>Source</label>
            <select name="source" id="source" class="form-control" required>
                <option value="">Select source</option>
                <option value="Direct">Direct</option>
                <option value="By Agent">By Agent</option>
            </select>
        </div>
    </div>

    {{-- Dynamic Rooms Dropdown --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <label>No Of Guests(Rooms)</label>
            <select name="guests" id="guests" class="form-control">
                <option value="">Select a room</option>
            </select>
        </div>
        <div class="col-md-6">
            <label>Leading Guest Name</label>
            <input type="text" name="customer_name" class="form-control" required>
        </div>
    </div>

    {{-- Remaining Booking Details --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label>Phone Number</label>
            <input type="text" name="phone_number" class="form-control" required>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label>Nationality</label>
            <input type="text" name="nationality" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label>Passport Number</label>
            <input type="text" name="passport_number" class="form-control" required>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label>Booking Status</label>
            <select name="booking_status" class="form-control" required> 
                <option value="">Select status</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
        <div class="col-md-6">
            <label>Pickup Location & Time</label>
            <input type="text" name="pickup_location_time" class="form-control" required>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label>Add-ons / Activities</label>
            <textarea name="addons" class="form-control" placeholder="e.g. Scuba diving, excursions" required></textarea>
        </div>
        <div class="col-md-6">
            <label>Assigned Agent</label>
            <select name="agent_id" id="agent_id" class="form-control" required>
                <option value="">Select Agent</option>
                @foreach($agents as $agent)
                    <option value="{{ $agent->id }}">{{ $agent->first_name }} {{ $agent->last_name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label>Notes</label>
            <textarea name="notes" class="form-control"></textarea>
        </div>
        <div class="col-md-6">
            <label>Comments</label>
            <textarea name="comments" class="form-control"></textarea>
        </div>
    </div>

    <div class="row mb-3">
        @if(auth()->user()->hasRole('admin'))
        <div class="col-md-6">
            <label for="company_id" class="form-label">Company</label>
            <select name="company_id" id="company_id" class="form-control" required>
                <option value="">Select Company</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                @endforeach
            </select>
        </div>
        @else
            <input type="hidden" name="company_id" value="{{ $companyId }}">
        @endif
    </div>

    <button type="submit" class="btn btn-primary">Create Booking</button>
</form>
        </div>
    </div>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
<script>
$(document).ready(function(){

    // Inline Trip Toggle
    $('#inlineTripCheckbox').on('change', function(){
        const checked = this.checked;
        $('#inlineTripFields').toggle(checked);
        $('#trip_id').prop('disabled', checked);
        $('#guests').html('<option value="">Select a room</option>');
    });

    // Set min dates
    const today = new Date().toISOString().split('T')[0];
    $('#start_date').attr('min', today);
    $('#end_date').attr('min', today);
    $('#start_date').on('change', function(){
        $('#end_date').attr('min', this.value);
    });

    // Dynamic Guests Dropdown for Existing Trip
    $('#trip_id').on('change', function(){
        const tripId = $(this).val();
        const guestsSelect = $('#guests');
        guestsSelect.html('<option>Loading...</option>');

        if(!tripId) {
            guestsSelect.html('<option value="">Select a room</option>');
            return;
        }

        $.ajax({
            url: `/trips/${tripId}/available-rooms`,
            type: 'GET',
            success: function(data){
                guestsSelect.html('<option value="">Select a room</option>');
                data.rooms.forEach(room => {
                    guestsSelect.append(`<option value="${room.id}">
                        ${room.name} — Capacity: ${room.capacity}, Price: $${room.price_per_day}
                    </option>`);
                });
            },
            error: function(){ guestsSelect.html('<option value="">Error loading rooms</option>'); }
        });
    });

    // Inline Trip Rooms Dropdown
    $('#boat, #trip_type, #start_date, #end_date').on('change', function(){
        const boat = $('#boat').val();
        const type = $('#trip_type').val();
        const start = $('#start_date').val();
        const end = $('#end_date').val();
        const guestSelect = $('#inline_guests');

        if(!boat || !type || !start || !end) return;

        $.ajax({
            url: '/boats/available-rooms',
            type: 'GET',
            data: { boat: boat, trip_type: type, start_date: start, end_date: end },
            success: function(data){
                guestSelect.empty().append('<option value="">Select a room</option>');
                data.rooms.forEach(room=>{
                    guestSelect.append(`<option value="${room.id}" data-price="${room.price_per_day}">
                        ${room.name} — Capacity: ${room.capacity}, Price: $${room.price_per_day}
                    </option>`);
                });
                $('#inlineGuestsRow').show();
            },
            error: function(){ alert('Error fetching rooms'); }
        });
    });

    // Set price for selected inline room
    $('#inline_guests').on('change', function(){
        const price = $(this).find('option:selected').data('price') || '';
        $('#price').val(price);
    });

    // Source & Agent logic
    $('#source').on('change', function(){
        const val = $(this).val();
        const agentSelect = $('#agent_id');
        const guestFields = ['customer_name','email','phone_number','nationality','passport_number'];
        if(val==='Direct'){
            agentSelect.prop('disabled', true);
            guestFields.forEach(f=>$(`input[name="${f}"]`).prop('disabled', false));
        } else if(val==='By Agent'){
            agentSelect.prop('disabled', false);
            guestFields.forEach(f=>$(`input[name="${f}"]`).prop('disabled', true));
        } else {
            agentSelect.prop('disabled', false);
            guestFields.forEach(f=>$(`input[name="${f}"]`).prop('disabled', false));
        }
    }).trigger('change');

});
</script>
@endsection
