@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
    <div class="container pt-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Booking Overview: {{ $trip->boat->name }} (Trip: {{ $trip->title }})</h2>

            @php
                $totalRooms = $trip->boat->rooms->count();
                $bookedRooms = $trip->bookings->count();
                $availableRooms = $totalRooms - $bookedRooms;
            @endphp

            @can('create-trip')
                @if($availableRooms > 0)
                    <a href="{{ route('bookings.create', ['trip_id' => $trip->id]) }}" class="btn btn-primary">Create Booking</a>
                @endif
            @endcan
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

        <!-- Progress Bar -->
        <div class="mb-4">
            <label>Rooms Booking Status:</label>
            <div class="progress">
                <div class="progress-bar {{ $availableRooms == 0 ? 'bg-danger' : 'bg-success' }}" role="progressbar"
                    style="width: {{ ($bookedRooms / $totalRooms) * 100 }}%" 
                    aria-valuenow="{{ $bookedRooms }}" aria-valuemin="0" aria-valuemax="{{ $totalRooms }}">
                    {{ $bookedRooms }} / {{ $totalRooms }} Booked
                </div>
            </div>
        </div>

        <!-- Table -->
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Room Name</th>
                    <th>Boat Name</th>
                    <th>Status</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Booked By</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trip->boat->rooms as $room)
                    @php
                        $booking = $trip->bookings->firstWhere('room_id', $room->id);
                    @endphp
                    <tr>
                        <td>{{ $room->room_name }}</td>
                        <td>{{ $trip->boat->name }}</td>
                        <td>
                            @if($booking)
                                {{ $booking->booking_status }}
                            @else
                                Available
                            @endif
                        </td>
                        <td>{{ $trip->start_date }}</td> 
                        <td>{{ $trip->end_date }}</td>
                        <td>{{ $booking->customer_name ?? '-' }}</td>
                        <td>
                            @if($booking)
                                <a href="{{ route('bookings.edit', $booking->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                <form action="{{ route('bookings.destroy', $booking->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            @else
                                <span class="text-muted">No Booking</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No booking available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Chart.js -->
        <!-- <canvas id="bookingChart" height="100" class="mt-4"></canvas> -->
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('bookingChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Booked', 'Available'],
        datasets: [{
            data: [{{ $bookedRooms }}, {{ $availableRooms }}],
            backgroundColor: ['#dc3545', '#28a745']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});
</script>
@endsection
