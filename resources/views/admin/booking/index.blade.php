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
    <th>Status</th>
    <th>Actions</th>
</tr>
</thead>
<tbody>
@foreach($bookings as $booking)
<tr>
    <td>{{ $booking->id }}</td>
    <td>#{{ $booking->slot_id }}</td>
    <td>{{ $booking->boat->name ?? '-' }}</td>
    <td>{{ $booking->room->room_name ?? '-' }}</td>
    <td>{{ $booking->guest_name }}</td>
<td>
    @if($booking->slot)
        {{ $booking->slot->start_date->format('Y-m-d') }} → {{ $booking->slot->end_date->format('Y-m-d') }}
    @else
        -
    @endif
</td>
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
