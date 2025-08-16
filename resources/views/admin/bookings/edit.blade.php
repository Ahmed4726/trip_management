@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4">Edit Booking</h2>
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
<form action="{{ route('bookings.update', $booking->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="trip_id">Select Trip</label>
            <select name="trip_id" id="trip_id" class="form-control" required>
                <option value="">Choose a trip</option>
                @foreach($trips as $trip)
                    <option value="{{ $trip->id }}" 
                        {{ $booking->trip_id == $trip->id ? 'selected' : '' }}>
                        {{ $trip->title }} ({{ $trip->start_date }} - {{ $trip->end_date }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label>Source</label>
            <select name="source" id="source" class="form-control" required>
                <option value="">Select source</option>
                <option value="Direct" {{ $booking->source == 'Direct' ? 'selected' : '' }}>Direct</option>
                <option value="By Agent" {{ $booking->source == 'By Agent' ? 'selected' : '' }}>By Agent</option>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label>No Of Guests(Rooms)</label>
<select id="guests" name="guests" class="form-control" data-current="{{ $booking->guests }}">
    <option value="">Select number of guests</option>
</select>

        </div>
        <div class="col-md-6">
            <label>Leading Guest Name</label>
            <input type="text" name="customer_name" class="form-control" value="{{ $booking->customer_name }}">
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ $booking->email }}">
        </div>
        <div class="col-md-6">
            <label>Phone Number</label>
            <input type="text" name="phone_number" class="form-control" value="{{ $booking->phone_number }}">
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label>Nationality</label>
            <input type="text" name="nationality" class="form-control" value="{{ $booking->nationality }}">
        </div>
        <div class="col-md-6">
            <label>Passport Number</label>
            <input type="text" name="passport_number" class="form-control" value="{{ $booking->passport_number }}">
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label>Booking Status</label>
            <select name="booking_status" class="form-control" required>
                <option value="pending" {{ $booking->booking_status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ $booking->booking_status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="cancelled" {{ $booking->booking_status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        <div class="col-md-6">
            <label>Pickup Location & Time</label>
            <input type="text" name="pickup_location_time" class="form-control" value="{{ $booking->pickup_location_time }}">
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label>Add-ons / Activities</label>
            <textarea name="addons" class="form-control">{{ $booking->addons }}</textarea>
        </div>
        <div class="col-md-6">
            <label>Assigned Agent</label>
            <select name="agent_id" id="agent_id" class="form-control">
                <option value="">Select Agent</option>
                @foreach($agents as $agent)
                    <option value="{{ $agent->id }}" {{ $booking->agent_id == $agent->id ? 'selected' : '' }}>
                        {{ $agent->first_name }} {{ $agent->last_name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label>Notes</label>
            <textarea name="notes" class="form-control">{{ $booking->notes }}</textarea>
        </div>
        <div class="col-md-6">
            <label>Comments</label>
            <textarea name="comments" class="form-control">{{ $booking->comments }}</textarea>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Update Booking</button>
</form>
        </div>
    </div>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tripSelect = document.getElementById('trip_id');
    const guestsSelect = document.getElementById('guests');
    const currentGuestValue = guestsSelect.getAttribute('data-current');

    function loadRooms(tripId, selectedValue = null) {
        guestsSelect.innerHTML = '<option value="">Loading...</option>';

        if (tripId) {
            fetch(`/trips/${tripId}/rooms`)
                .then(response => response.json())
                .then(data => {
                    guestsSelect.innerHTML = '<option value="">Select number of guests</option>';

                    // Add available rooms
                    data.rooms.forEach(room => {
                        const selected = (selectedValue == room) ? 'selected' : '';
                        guestsSelect.innerHTML += `<option value="${room}" ${selected}>${room}</option>`;
                    });

                    // Ensure the old value is selectable, even if not available
                    if (selectedValue && !data.rooms.includes(parseInt(selectedValue))) {
                        guestsSelect.innerHTML += `<option value="${selectedValue}" selected>${selectedValue} (Currently booked)</option>`;
                    }
                })
                .catch(() => {
                    guestsSelect.innerHTML = '<option value="">Error loading rooms</option>';
                });
        } else {
            guestsSelect.innerHTML = '<option value="">Select number of guests</option>';
        }
    }

    // On trip change
    tripSelect.addEventListener('change', function () {
        loadRooms(this.value, null);
    });

    // On page load (edit mode)
    if (tripSelect.value) {
        loadRooms(tripSelect.value, currentGuestValue);
    }
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


</script>
@endsection
