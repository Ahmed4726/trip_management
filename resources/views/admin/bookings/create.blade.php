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
                @error('inline_trip') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            {{-- Inline Trip Fields --}}
            <div id="inlineTripFields" style="display:none; border:1px solid #ccc; padding:15px; border-radius:5px;">
                <div class="row">
                    <div class="col-md-4">
                        <label>Trip Title</label>
                        <input type="text" name="trip_title" class="form-control" required>
                        @error('trip_title') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4">
                        <label>Boat</label>
                        <select name="boat_id" id="inline_boat" class="form-control" required>
                            <option value="">Select boat</option>
                            @foreach($boats as $boat)
                                <option value="{{ $boat->id }}">{{ $boat->name }}</option>
                            @endforeach
                        </select>
                        @error('boat_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4">
                        <label>Trip Type</label>
                        <select id="trip_type" name="trip_type" class="form-control" required>
                            <option value="open">Open Trip</option>
                            <option value="private">Private Charter</option>
                        </select>
                        @error('trip_type') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-6">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="Available">Available</option>
                            <option value="On Hold">On Hold</option>
                            <option value="Booked">Booked</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Docking">Docking</option>
                        </select>
                        @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6">
                        <label>Rate Plan</label>
                        <select name="rate_plan_id" class="form-control" required>
                            <option value="">Select Rate Plan</option>
                            @foreach($ratePlans as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }} ({{ $plan->currency }})</option>
                            @endforeach
                        </select>
                        @error('rate_plan_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-6">
                        <label>Payment Policy</label>
                        <select name="payment_policy_id" class="form-control" required>
                            <option value="">Select Payment Policy</option>
                            @foreach($paymentPolicies as $policy)
                                <option value="{{ $policy->id }}">{{ $policy->name }} (DP: {{ $policy->dp_percent }}%)</option>
                            @endforeach
                        </select>
                        @error('payment_policy_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6">
                        <label>Cancellation Policy</label>
                        <select name="cancellation_policy_id" class="form-control" required>
                            <option value="">Select Cancellation Policy</option>
                            @foreach($cancellationPolicies as $policy)
                                <option value="{{ $policy->id }}">{{ $policy->name }}</option>
                            @endforeach
                        </select>
                        @error('cancellation_policy_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-6">
                        <label>Start Date</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" required>
                        @error('start_date') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6">
                        <label>End Date</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" required>
                        @error('end_date') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                @if(auth()->user()->hasRole('admin'))
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label>Company</label>
                        <select name="company_id" class="form-control" required>
                            <option value="">Select Company</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                        @error('company_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                @else
                    <input type="hidden" name="company_id" value="{{ $companyId }}">
                @endif

                <div class="row mt-2">
                    <div class="col-md-12">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control"></textarea>
                        @error('notes') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Inline Rooms - Only show if Open Trip --}}
                <div class="row mt-2" id="inlineGuestsRow">
                    <div class="col-md-6">
                        <label>Room Selection</label>
                        <select name="room_id" id="inline_guests" class="form-control" required>
                            <option value="">Select a room</option>
                        </select>
                        @error('room_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6">
                        <label>Room Price</label>
                        <input type="text" name="price" id="inline_room_price" class="form-control" readonly>
                        @error('inline_room_price') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Existing Trip Selection --}}
            <div class="row mb-3 mt-3">
                <div class="col-md-6">
                    <label for="trip_id">Select Slot</label>
                    <select name="trip_id" id="trip_id" class="form-control" required>
                        <option value="">Choose a trip</option>
                        @foreach($trips as $trip)
                            <option value="{{ $trip->id }}">{{ $trip->title }} ({{ $trip->start_date }} - {{ $trip->end_date }})</option>
                        @endforeach
                    </select>
                    @error('trip_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6">
                    <label>Direct/Agent</label>
                    <select name="source" id="source" class="form-control" required>
                        <option value="">Select</option>
                        <option value="Direct">Direct</option>
                        <option value="By Agent">By Agent</option>
                    </select>
                    @error('source') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Dynamic Rooms for Existing Trip --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>No Of Guests(Rooms)</label>
                    <select name="room_id" id="guests" class="form-control">
                        <option value="">Select a room</option>
                    </select>
                    @error('room_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6">
                    <label>Leading Guest Name</label>
                    <input type="text" name="customer_name" class="form-control" required>
                    @error('customer_name') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Booking Details --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                    @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6">
                    <label>Phone Number</label>
                    <input type="text" name="phone_number" class="form-control" required>
                    @error('phone_number') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Nationality</label>
                    <input type="text" name="nationality" class="form-control" required>
                    @error('nationality') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6">
                    <label>Passport Number</label>
                    <input type="text" name="passport_number" class="form-control" required>
                    @error('passport_number') <span class="text-danger">{{ $message }}</span> @enderror
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
                    @error('booking_status') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6">
                    <label>Pickup Location & Time</label>
                    <input type="text" name="pickup_location_time" class="form-control" required>
                    @error('pickup_location_time') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Add-ons / Activities</label>
                    <textarea name="addons" class="form-control" placeholder="e.g. Scuba diving, excursions" required></textarea>
                    @error('addons') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6">
                    <label>Assigned Agent</label>
                    <select name="agent_id" id="agent_id" class="form-control" required>
                        <option value="">Select Agent</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}">{{ $agent->first_name }} {{ $agent->last_name }}</option>
                        @endforeach
                    </select>
                    @error('agent_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Notes</label>
                    <textarea name="notes_booking" class="form-control"></textarea>
                    @error('notes_booking') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6">
                    <label>Comments</label>
                    <textarea name="comments" class="form-control"></textarea>
                    @error('comments') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
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

        // Enable/disable inputs inside inline trip section
        $('#inlineTripFields').find('input, select, textarea').prop({
            disabled: !checked,
            required: checked
        });

        // Disable existing trip fields if inline trip is checked
        $('#trip_id, #guests').prop({
            disabled: checked,
            required: !checked
        });

        // Reset room dropdown
        $('#guests').html('<option value="">Select a room</option>');
    }).trigger('change');

    // Set min dates for inline trip
    const today = new Date().toISOString().split('T')[0];
    $('#start_date, #end_date').attr('min', today);
    $('#start_date').on('change', function(){
        $('#end_date').attr('min', this.value);
    });

    // Dynamic Guests Dropdown for Existing Trip
    $('#trip_id').on('change', function(){
        const tripId = $(this).val();
        const guestsSelect = $('#guests');

        guestsSelect.html('<option>Loading...</option>');

        if(!tripId){
            guestsSelect.html('<option value="">Select a room</option>');
            return;
        }

        $.ajax({
            url:`/trips/${tripId}/available-rooms`,
            type:'GET',
            success:function(data){
                guestsSelect.html('<option value="">Select a room</option>');
                data.rooms.forEach(room=>{
                    guestsSelect.append(`<option value="${room.id}">${room.name} — Capacity: ${room.capacity}, Price: $${room.price_per_day}</option>`);
                });
            },
            error:function(){
                guestsSelect.html('<option value="">Error loading rooms</option>');
            }
        });
    });

    // Inline Trip Rooms Dropdown
    $('#inline_boat, #trip_type, #start_date, #end_date').on('change', function(){
        const boat = $('#inline_boat').val();
        const type = $('#trip_type').val();
        const start = $('#start_date').val();
        const end = $('#end_date').val();
        const guestSelect = $('#inline_guests');

        if(!boat || !type || !start || !end) return;

        if(type === 'private') {
            // Hide rooms for private charter
            $('#inlineGuestsRow').hide();
            guestSelect.prop('required', false);
            guestSelect.empty().append('<option value="">Full boat charter</option>');
            $('#inline_room_price').val('');
            return;
        } else {
            $('#inlineGuestsRow').show();
            guestSelect.prop('required', true);
        }

        $.ajax({
            url: '/boats/available-rooms',
            type: 'GET',
            data: { boat: boat, trip_type: type, start_date: start, end_date: end },
            success: function(data){
                guestSelect.empty().append('<option value="">Select a room</option>');
                if(data.rooms.length){
                    data.rooms.forEach(room => {
                        guestSelect.append(`<option value="${room.id}" data-price="${room.price_per_day}">${room.name} — Capacity: ${room.capacity}, Price: $${room.price_per_day}</option>`);
                    });
                } else {
                    guestSelect.append('<option value="">No rooms available</option>');
                }
            },
            error: function(xhr, status, error){
                console.error('Error fetching rooms:', error);
                guestSelect.empty().append('<option value="">Error fetching rooms</option>');
            }
        });
    });

    // Set price for selected inline room
    $('#inline_guests').on('change', function(){
        const price = $(this).find('option:selected').data('price') || '';
        $('#inline_room_price').val(price);
    });

    // Source & Agent logic
    $('#source').on('change', function(){
        const val = $(this).val();
        const agentSelect = $('#agent_id');
        const guestFields = ['customer_name','email','phone_number','nationality','passport_number'];
        if(val==='Direct'){
            agentSelect.prop('disabled', true).prop('required', false);
            guestFields.forEach(f=>$(`input[name="${f}"]`).prop('disabled', false).prop('required', true));
        } else if(val==='By Agent'){
            agentSelect.prop('disabled', false).prop('required', true);
            guestFields.forEach(f=>$(`input[name="${f}"]`).prop('disabled', true).prop('required', false));
        } else {
            agentSelect.prop('disabled', false).prop('required', true);
            guestFields.forEach(f=>$(`input[name="${f}"]`).prop('disabled', false).prop('required', true));
        }
    }).trigger('change');

});
</script>
@endsection
