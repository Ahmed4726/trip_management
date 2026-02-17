@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
    <div class="container pt-3">
        <div class="d-flex justify-content-between mb-3">
            <h4>Bookings</h4>
            <a href="{{ route('admin.bookings.create') }}" class="btn btn-primary">Create Booking</a>
        </div>

        @foreach (['success','error'] as $msg)
            @if(session($msg))
                <div class="alert alert-{{ $msg == 'success' ? 'success' : 'danger' }}">
                    {{ session($msg) }}
                </div>
            @endif
        @endforeach

        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light text-uppercase small">
                    <tr>
                        <th>ID</th>
                        <th>Slot</th>
                        <th>Boat</th>
                        <th>Room</th>
                        <th>Guest</th>
                        <th>Dates</th>
                        <th>Price (USD)</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                    <tr>
                        <td>{{ $booking->id }}</td>
                        <td>#{{ $booking->slot_id }}</td>
                    <td>
                        @if(!empty($booking->boats) && $booking->boats->count() > 0)
                            <ul class="mb-0">
                                @foreach($booking->boats as $boat)
                                    <li>{{ $boat->name }}</li>
                                @endforeach
                            </ul>
                        @else
                            {{ $booking->boat->name ?? '-' }}
                        @endif
                    </td>

                    <td>
                        @if(!empty($booking->rooms) && $booking->rooms->count() > 0)
                            <ul class="mb-0">
                                @foreach($booking->rooms as $room)
                                    <li>{{ $room->room_name }}</li>
                                @endforeach
                            </ul>
                        @else
                            {{ $booking->room->room_name ?? '-' }}
                        @endif
                    </td>

                        <td>{{ $booking->guest_name }}</td>
                    <td>
                        @if($booking->slot)
                            {{ $booking->slot->start_date->format('d-m-Y') }} → {{ $booking->slot->end_date->format('d-m-Y') }}
                        @else
                            -
                        @endif
                    </td>
                        <td>{{ $booking->price_usd }}</td>
                        <td>{{ $booking->status }}</td>
                        <td>
                            <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form method="POST" action="{{ route('admin.bookings.destroy', $booking) }}" class="d-inline" onsubmit="return confirm('Delete this booking?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
        </table>
    </div>
</div>
@endsection
