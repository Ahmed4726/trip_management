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
            <label>Customer Name</label>
            <input type="text" name="customer_name" class="form-control" required>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label>Guests</label>
            <input type="number" name="guests" class="form-control" value="1" min="1" required>
        </div>
        <div class="col-md-6">
            <label>Source</label>
            <input type="text" name="source" class="form-control" required>
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
            <input type="text" name="addons" class="form-control" placeholder="e.g. Scuba diving, excursions" required>
        </div>
        <div class="col-md-6">
            <label>Room or Cabin Preference</label>
            <select name="room_preference" class="form-control" required>
                <option value="">Select preference</option>
                <option value="single">Single</option>
                <option value="double">Double</option>
                <option value="suite">Suite</option>
            </select>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label>Assigned Agent</label>
            <select name="agent_id" class="form-control" required>
                <option value="">Select Agent</option>
                @foreach($agents as $agent)
                    <option value="{{ $agent->id }}">{{ $agent->first_name }} {{ $agent->last_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label>Comments</label>
            <textarea name="comments" class="form-control" ></textarea>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label>Notes</label>
            <textarea name="notes" class="form-control"></textarea>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Create Booking</button>
</form>




        </div>
    </div>
</div>
</div>
<script>
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
