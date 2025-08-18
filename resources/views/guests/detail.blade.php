@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="container pt-3">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 mb-0">Guest Details</h2>
            <a href="{{ route('bookings.index') }}" class="btn btn-secondary">
                Back
            </a>
        </div>

        {{-- Guest Info --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user"></i> Guest Information</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-md-3">Trip</dt>
                    <dd class="col-md-9">{{ $guest->trip ? $guest->trip->title : 'N/A' }}</dd>

                    <dt class="col-md-3">Name</dt>
                    <dd class="col-md-9">{{ $guest->name ?? '-' }}</dd>

                    <dt class="col-md-3">Gender</dt>
                    <dd class="col-md-9">{{ ucfirst($guest->gender) ?? '-' }}</dd>

                    <dt class="col-md-3">Email</dt>
                    <dd class="col-md-9">{{ $guest->email ?? '-' }}</dd>

                    <dt class="col-md-3">Date of Birth</dt>
                    <dd class="col-md-9">{{ $guest->dob ?? '-' }}</dd>

                    <dt class="col-md-3">Passport</dt>
                    <dd class="col-md-9">{{ $guest->passport ?? '-' }}</dd>

                    <dt class="col-md-3">Nationality</dt>
                    <dd class="col-md-9">{{ $guest->nationality ?? '-' }}</dd>

                    <dt class="col-md-3">Cabin</dt>
                    <dd class="col-md-9">{{ $guest->cabin ?? '-' }}</dd>

                    <dt class="col-md-3">Surf Level</dt>
                    <dd class="col-md-9">{{ $guest->surfLevel ?? '-' }}</dd>

                    <dt class="col-md-3">Board Details</dt>
                    <dd class="col-md-9">{{ $guest->boardDetails ?? '-' }}</dd>
                </dl>
            </div>
        </div>

        {{-- Arrival Info --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-plane-arrival"></i> Arrival Information</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-md-3">Date</dt>
                    <dd class="col-md-9">{{ $guest->arrivalFlightDate ?? '-' }}</dd>

                    <dt class="col-md-3">Flight Number</dt>
                    <dd class="col-md-9">{{ $guest->arrivalFlightNumber ?? '-' }}</dd>

                    <dt class="col-md-3">Airport</dt>
                    <dd class="col-md-9">{{ $guest->arrivalAirport ?? '-' }}</dd>

                    <dt class="col-md-3">Time</dt>
                    <dd class="col-md-9">{{ $guest->arrivalTime ?? '-' }}</dd>

                    <dt class="col-md-3">Hotel Pickup</dt>
                    <dd class="col-md-9">{{ $guest->hotelPickup ?? '-' }}</dd>
                </dl>
            </div>
        </div>

        {{-- Departure Info --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-plane-departure"></i> Departure Information</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-md-3">Date</dt>
                    <dd class="col-md-9">{{ $guest->departureFlightDate ?? '-' }}</dd>

                    <dt class="col-md-3">Flight Number</dt>
                    <dd class="col-md-9">{{ $guest->departureFlightNumber ?? '-' }}</dd>

                    <dt class="col-md-3">Airport</dt>
                    <dd class="col-md-9">{{ $guest->departureAirport ?? '-' }}</dd>

                    <dt class="col-md-3">Time</dt>
                    <dd class="col-md-9">{{ $guest->departureTime ?? '-' }}</dd>
                </dl>
            </div>
        </div>

        {{-- Other Information --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="fas fa-notes-medical"></i> Additional Information</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-md-3">Medical / Dietary</dt>
                    <dd class="col-md-9">{{ $guest->medicalDietary ?? '-' }}</dd>

                    <dt class="col-md-3">Special Requests</dt>
                    <dd class="col-md-9">{{ $guest->specialRequests ?? '-' }}</dd>
                </dl>
            </div>
        </div>

        {{-- Insurance --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-file-medical"></i> Insurance</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-md-3">Insurance Name</dt>
                    <dd class="col-md-9">{{ $guest->insuranceName ?? '-' }}</dd>

                    <dt class="col-md-3">Policy Number</dt>
                    <dd class="col-md-9">{{ $guest->policyNumber ?? '-' }}</dd>
                </dl>
            </div>
        </div>

        {{-- Emergency Contact --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-user-shield"></i> Emergency Contact</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-md-3">Name</dt>
                    <dd class="col-md-9">{{ $guest->emergencyName ?? '-' }}</dd>

                    <dt class="col-md-3">Relation</dt>
                    <dd class="col-md-9">{{ $guest->emergencyRelation ?? '-' }}</dd>

                    <dt class="col-md-3">Phone</dt>
                    <dd class="col-md-9">{{ $guest->emergencyPhone ?? '-' }}</dd>
                </dl>
            </div>
        </div>

        {{-- Guest Contact --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="fas fa-phone-alt"></i> Guest Contact</h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-md-3">WhatsApp</dt>
                    <dd class="col-md-9">{{ $guest->guestWhatsapp ?? '-' }}</dd>

                    <dt class="col-md-3">Email</dt>
                    <dd class="col-md-9">{{ $guest->guestEmail ?? '-' }}</dd>
                </dl>
            </div>
        </div>

    </div>
</div>
@endsection
