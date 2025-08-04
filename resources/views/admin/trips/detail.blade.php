@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="container mt-4">
        <h2 class="mb-4">Trip Details</h2>

        <div class="card">
            <div class="card-header">
                <strong>Trip Information</strong>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Title:</strong> {{ $trip->title }}
                    </div>
                    <div class="col-md-6">
                        <strong>Region:</strong> {{ $trip->region }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Status:</strong> {{ $trip->status }}
                    </div>
                    <div class="col-md-6">
                        <strong>Trip Type:</strong> {{ $trip->trip_type }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Start Date:</strong> {{ $trip->start_date }}
                    </div>
                    <div class="col-md-6">
                        <strong>End Date:</strong> {{ $trip->end_date }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Price:</strong> ${{ $trip->price }}
                    </div>
                    <div class="col-md-6">
                        <strong>Boat:</strong> {{ $trip->boat }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Guests:</strong> {{ $trip->guests }}
                    </div>
                    <div class="col-md-6">
                        <strong>Leading Guest ID:</strong> {{ $trip->leading_guest_id }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Agent Name:</strong>
                        {{ $trip->agent ? $trip->agent->first_name . ' ' . $trip->agent->last_name : '-' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Notes:</strong> {{ $trip->notes }}
                    </div>
                </div>
            </div>
            <div class="card-footer">
             <a href="{{ route('trips.index') }}" class="btn btn-secondary">Back to Trips</a>
             </div>
        </div>

       
    </div>
</div>
@endsection
