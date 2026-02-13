@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container pt-3">

<h4>View Slot</h4>

<div class="card mb-3">
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-md-6"><strong>Template:</strong> {{ $slot->template->product_name ?? '-' }}</div>
            <div class="col-md-6"><strong>Slot Type:</strong> {{ $slot->slot_type }}</div>
        </div>
        <div class="row mb-2">
            <div class="col-md-6"><strong>Status:</strong> {{ $slot->status }}</div>
            <div class="col-md-6"><strong>Region:</strong> {{ $slot->region->name ?? '-' }}</div>
        </div>
        <div class="row mb-2">
            <div class="col-md-6">
                <strong>Vessels:</strong>
                @php
                    $boats = collect([]);
                    if($slot->boat) $boats->push($slot->boat->name);
                    if($slot->boats->count()) $boats = $boats->merge($slot->boats->pluck('name'));
                @endphp
                {{ $boats->join(', ') ?: '-' }}
            </div>
            <div class="col-md-6"><strong>Departure → Arrival:</strong> {{ $slot->departurePort->name ?? '-' }} → {{ $slot->arrivalPort->name ?? '-' }}</div>
        </div>
        <div class="row mb-2">
            <div class="col-md-6"><strong>Start → End:</strong> {{ $slot->start_date->format('d F Y') }} → {{ $slot->end_date->format('d F Y') }}</div>
            <div class="col-md-6"><strong>Duration (Nights):</strong> {{ $slot->duration_nights }}</div>
        </div>
        <div class="row mb-2">
            <div class="col-md-12"><strong>Notes:</strong> {{ $slot->notes ?: '-' }}</div>
        </div>
        <a href="{{ route('admin.slots.index') }}" class="btn btn-secondary mt-2">Back to Slots</a>
    </div>
</div>

<h5>Associated Bookings</h5>
<table class="table table-bordered table-striped table-hover">
    <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>Guest Name</th>
            <th>Room(s)</th>
            <th>Pax</th>
            <th>Status</th>
            <th>Price (USD)</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($slot->bookings as $booking)
            <tr>
                <td>{{ $booking->id }}</td>
                <td>{{ $booking->guest_name }}</td>

                {{-- Rooms --}}
                <td>
                    @php
                        // Get all rooms assigned via BookingGuestRoom
                        $assignedRooms = $booking->guestRoomAssignments->pluck('room.room_name')->unique();
                        // Include single room if exists
                        if($booking->room) $assignedRooms->push($booking->room->room_name);
                    @endphp
                    {{ $assignedRooms->join(', ') ?: '-' }}
                </td>

                {{-- Pax (Total Guests) --}}
                <td>
    @php
        $pax = collect($booking->guestRoomAssignments)->count();
        if(!$pax) {
            $pax = collect($booking->guests)->count() ?: 1;
        }
    @endphp
    {{ $pax }}
</td>


                {{-- Status --}}
                <td>
                    @if($booking->status == 'DP Paid')
                        <span class="badge bg-success">DP Paid</span>
                    @elseif($booking->status == 'Pending')
                        <span class="badge bg-warning text-dark">Pending</span>
                    @elseif($booking->status == 'Canceled')
                        <span class="badge bg-danger">Canceled</span>
                    @endif
                </td>

                <td>${{ $booking->price }}</td>
                <td>
                    <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn btn-sm btn-warning">Edit</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">No bookings found for this slot.</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <th colspan="5" class="text-end">Total Price:</th>
            <th colspan="2">${{ $slot->bookings->sum('price') }}</th>
        </tr>
    </tfoot>
</table>


</div>
</div>
@endsection
