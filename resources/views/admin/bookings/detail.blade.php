@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="container pt-3">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">Booking Details</h2>
            <a href="{{ route('bookings.index') }}" class="btn btn-secondary">Back to Bookings</a>
        </div>

        {{-- Booking Info --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Booking Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-6"><strong>Trip:</strong> {{ $booking->trip->title ?? '-' }}</div>
                    <div class="col-md-6"><strong>Customer Name:</strong> {{ $booking->customer_name ?? '-' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-6"><strong>Guests (Rooms):</strong> {{ $booking->guests ?? '-' }}</div>
                    <div class="col-md-6"><strong>Source:</strong> {{ $booking->source ?? '-' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-6"><strong>Email:</strong> {{ $booking->email ?? '-' }}</div>
                    <div class="col-md-6"><strong>Phone Number:</strong> {{ $booking->phone_number ?? '-' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-6"><strong>Nationality:</strong> {{ $booking->nationality ?? '-' }}</div>
                    <div class="col-md-6"><strong>Passport Number:</strong> {{ $booking->passport_number ?? '-' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-6"><strong>Status:</strong> {{ ucfirst($booking->booking_status) ?? '-' }}</div>
                    <div class="col-md-6"><strong>Pickup Location & Time:</strong> {{ $booking->pickup_location_time ?? '-' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-6"><strong>Add-ons:</strong> {{ $booking->addons ?? '-' }}</div>
                    <!-- <div class="col-md-6"><strong>Room Preference:</strong> {{ $booking->room_preference ?? '-' }}</div> -->
                 <div class="col-md-6">
                    <strong>Agent:</strong>
                        {{ $booking->agent ? $booking->agent->first_name . ' ' . $booking->agent->last_name : '-' }}
                        </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-6">
                       <strong>Comments:</strong> {{ $booking->comments ?? '-' }}
                    </div>
                    <div class="col-md-6"><strong>Notes:</strong> {{ $booking->notes ?? '-' }}</div>
                </div>
                <div class="row mb-2">
                    
                </div>
            </div>
        </div>

        {{-- Tabs for Guests --}}
        <ul class="nav nav-tabs mb-3" id="bookingTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="guests-tab" data-toggle="tab" href="#guests" role="tab">Guests</a>
            </li>
        </ul>

        <div class="tab-content" id="bookingTabsContent">
            {{-- Guests Tab --}}
            <div class="tab-pane fade show active" id="guests" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Guests List</h5>
                    </div>
                   
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
