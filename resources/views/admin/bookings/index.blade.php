@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
    <div class="container pt-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">Manage Bookings</h2>
            <a href="{{ route('bookings.create') }}" class="btn btn-primary">Create Booking</a>
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
                <div class="table-responsive">
                    <!-- <div class="row mb-4">
                        <div class="col-md-3">
                            <label>Boat</label>
                            <select id="filterBoat" class="form-control">
                                <option value="">Select boat</option>
                                <optgroup label="Samara 1 (5 rooms)">
                                    <option value="Rinca">Rinca</option>
                                    <option value="Komodo">Komodo</option>
                                    <option value="Padar">Padar</option>
                                    <option value="Kanawa">Kanawa</option>
                                    <option value="Kelor">Kelor</option>
                                </optgroup>
                                <optgroup label="Samara 1 (4 rooms)">
                                    <option value="Room1">Room1</option>
                                    <option value="Room2">Room2</option>
                                    <option value="Room3">Room3</option>
                                    <option value="Room4">Room4</option>
                                </optgroup>
                                <optgroup label="Mischief (5 rooms)">
                                    <option value="Room1">Room1</option>
                                    <option value="Room2">Room2</option>
                                    <option value="Room3">Room3</option>
                                    <option value="Room4">Room4</option>
                                    <option value="Room5">Room5</option>
                                </optgroup>
                                <optgroup label="Samara (6 rooms)">
                                    <option value="Room1">Room1</option>
                                    <option value="Room2">Room2</option>
                                    <option value="Room3">Room3</option>
                                    <option value="Room4">Room4</option>
                                    <option value="Room5">Room5</option>
                                    <option value="Room6">Room6</option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Region</label>
                            <input type="text" id="filterRegion" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label>Status</label>
                            <select id="filterStatus" class="form-control">
                                <option value="">Select status</option>
                                <option value="Available">Available</option>
                                <option value="On Hold">On Hold</option>
                                <option value="Booked">Booked</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Start Date</label>
                            <input type="date" id="filterStartDate" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label>End Date</label>
                            <input type="date" id="filterEndDate" class="form-control">
                        </div>
                    </div> -->

                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light text-uppercase small">
                            <tr>
                                <th>#</th>
                                <!-- <th>Source</th> -->
                                <th>Customer Name</th>
                                <th>Status</th>
                                <th>Agent Name</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <!-- <th>Comments</th> -->
                                <!-- <th>Notes</th> -->
                                <th class="col-2">Link/UUID</th>
                                <!-- <th class="text-center">Actions</th> -->
                            </tr>
                        </thead>
                     <tbody id="tripTableBody">
    @forelse ($bookings as $index => $booking)
        <tr>
            <td>{{ $index + 1 }}</td>
            <!-- <td>{{ $booking->source ?? '—' }}</td> -->
            <td>{{ $booking->customer_name ?? '—' }}</td>
            <td>{{ $booking->booking_status ?? '—' }}</td>
            <td>{{ $booking->agent->first_name  ?? '—' }} {{ $booking->agent->last_name }}</td>
            <td>{{ $booking->trip->start_date ?? '—' }}</td>
            <td>{{ $booking->trip->end_date ?? '—' }}</td>
            <!-- <td>{{ $booking->comments ?? '—' }}</td> -->
            <!-- <td>{{ $booking->notes ?? '—' }}</td> -->
            <td>
                <button class="btn btn-sm btn-outline-primary" onclick="copyText({{ $booking->id }})">
                    Copy Link
                </button>
                <span id="linkText{{ $booking->id }}" class="d-none">
                    {{ route('guest.form', $booking->token) }}
                </span>
            </td>
            
        </tr>
    @empty
        <tr>
            <td colspan="11" class="text-center">No Bookings available</td>
        </tr>
    @endforelse
</tbody>


                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function fetchTrips() {
    $.ajax({
        url: "{{ route('trips.filter') }}",
        method: "GET",
        data: {
            boat: $('#filterBoat').val(),
            region: $('#filterRegion').val(),
            status: $('#filterStatus').val(),
            start_date: $('#filterStartDate').val(),
            end_date: $('#filterEndDate').val(),
        },
        success: function (response) {
            $('#tripTableBody').html(response.html);
        },
        error: function () {
            alert('Something went wrong!');
        }
    });
}

$('#filterBoat, #filterRegion, #filterStatus, #filterStartDate, #filterEndDate').on('change', fetchTrips);

function copyText(id) {
    const span = document.getElementById('linkText' + id);
    const text = span.innerText;

    const temp = document.createElement('textarea');
    temp.value = text;
    document.body.appendChild(temp);
    temp.select();
    document.execCommand('copy');
    document.body.removeChild(temp);

    Swal.fire({
        icon: 'success',
        title: 'Link copied!',
        showConfirmButton: false,
        timer: 1500
    });
}
</script>
@endsection
