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
    <div class="row mb-3">
    <div class="col-md-12">
        <div class="form-check mb-2">
            <input type="checkbox" name="inline_trip" id="inlineTripCheckbox" class="form-check-input">
            <label for="inlineTripCheckbox" class="form-check-label">Create New Trip Inline</label>
        </div>

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
                <option value="Samara 1 (5 rooms)">Samara 1 (5 rooms)</option>
                <option value="Samara 1 (4 rooms)">Samara 1 (4 rooms)</option>
                <option value="Mischief (5 rooms)">Mischief (5 rooms)</option>
                <option value="Samara (6 rooms)">Samara (6 rooms)</option>
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
                <option value="">Select guests</option>
            </select>
        </div>
        <div class="col-md-6">
            <label>Price</label>
            <input type="number" name="price" class="form-control" id="price">
        </div>
    </div>
        <div class="row mt-2">
        <div class="col-md-6">
            <label>Region</label>
            <input type="text" name="region" class="form-control" id="region">
        </div>
    </div>
</div>
    </div>
</div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="trip_id">Select Trip</label>
            <select name="trip_id" id="trip_id" class="form-control" required>
                <option value="">Choose a trip</option>
                @foreach($trips as $trip)
                    <option value="{{ $trip->id }}">
                        {{ $trip->title }} ({{ $trip->start_date }} - {{ $trip->end_date }})
                    </option>
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

    <div class="row mb-3">
       <div class="col-md-6">
    <label>No Of Guests(Rooms) </label>
    <select name="guests" id="guests" class="form-control">
        <option value="">Select number of guests</option>
    </select>
</div>

  <div class="col-md-6">
            <label>Leading guest Name</label>
            <input type="text" name="customer_name" class="form-control" required>
        </div>

    </div>

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
        <!-- <div class="col-md-6">
            <label>Room or Cabin Preference</label>
            <select name="room_preference" class="form-control" required>
                <option value="">Select preference</option>
                <option value="single">Single</option>
                <option value="double">Double</option>
                <option value="suite">Suite</option>
            </select>
        </div> -->
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
            <textarea name="comments" class="form-control" ></textarea>
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
<script src="https://code.jquery.com/jquery-3.6.1.min.js" ></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tripSelect = document.getElementById('trip_id');
    const guestsSelect = document.getElementById('guests');

    tripSelect.addEventListener('change', function () {
        const tripId = this.value;
        guestsSelect.innerHTML = '<option value="">Loading...</option>';

        if (tripId) {
            fetch(`/trips/${tripId}/rooms`)
                .then(response => response.json())
                .then(data => {
                    guestsSelect.innerHTML = '<option value="">Select number of guests</option>';
                    data.rooms.forEach((room, index) => {
                        guestsSelect.innerHTML += `<option value="${room}">Guest ${index+1} — Room ${room}</option>`;
                    });
                })
                .catch(() => {
                    guestsSelect.innerHTML = '<option value="">Error loading rooms</option>';
                });
        } else {
            guestsSelect.innerHTML = '<option value="">Select number of guests</option>';
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const sourceSelect = document.querySelector('select[name="source"]');
    const agentSelect = document.querySelector('select[name="agent_id"]');
    
    // Fields to disable when Source = "By Agent"
    const guestFields = [
        document.querySelector('input[name="customer_name"]'),
        document.querySelector('input[name="email"]'),
        document.querySelector('input[name="phone_number"]'),
        document.querySelector('input[name="nationality"]'),
        document.querySelector('input[name="passport_number"]')
    ];

    function toggleFields() {
        const sourceValue = sourceSelect.value;

        if (sourceValue === "Direct") {
            agentSelect.disabled = true;
            guestFields.forEach(field => field.disabled = false);
        } else if (sourceValue === "By Agent") {
            agentSelect.disabled = false;
            guestFields.forEach(field => field.disabled = true);
        } else {
            // Default: both enabled
            agentSelect.disabled = false;
            guestFields.forEach(field => field.disabled = false);
        }
    }

    // Run on load
    toggleFields();

    // Listen for changes
    sourceSelect.addEventListener('change', toggleFields);
});
     document.addEventListener('DOMContentLoaded', function () {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById("start_date").setAttribute("min", today);
        document.getElementById("end_date").setAttribute("min", today);
    });
    document.getElementById("start_date").addEventListener("change", function () {
    const selectedStart = this.value;
    document.getElementById("end_date").setAttribute("min", selectedStart);
});

    $('#inlineTripCheckbox').on('change', function() {
        $('#inlineTripFields').toggle(this.checked);
    });


    $('#inlineTripCheckbox').on('change', function() {
    const checked = this.checked;
    $('#inlineTripFields').toggle(checked);

    // Disable/enable trip selection
    $('#trip_id').prop('disabled', checked);

    // Reset guests select when switching
    $('#guests').html('<option value="">Select number of guests</option>');
    // $('#guests').prop('disabled', checked);
});

$('#boat, #trip_type, #start_date, #end_date').on('change', function() {
    const boat = $('#boat').val();
    const type = $('#trip_type').val();

    if (!boat || !type) return;

    $.ajax({
        url: '/boats/rooms',
        method: 'GET',
        data: { boat: boat, trip_type: type },
        success: function(data) {
            const guestSelect = $('#inline_guests');
            guestSelect.empty();
            guestSelect.append('<option value="">Select guests</option>');

            if (type === 'open') {
                data.rooms.forEach((room, idx) => {
                    guestSelect.append(`<option value="${room}">Guest ${idx+1} — Room ${room}</option>`);
                });
            } else if (type === 'private') {
                data.rooms.forEach((room) => {
                    guestSelect.append(`<option value="${room}" selected>Room ${room}</option>`);
                });
            }

            $('#inlineGuestsRow').show();
        },
        error: function() {
            alert('Error fetching rooms for this boat');
        }
    });
});


</script>
@endsection
