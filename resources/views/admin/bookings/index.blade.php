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
                <form id="filterForm" class="row g-2 mb-3">
    <div class="col-md-2">
        <input type="text" name="customer_name" id="filterCustomer" class="form-control" placeholder="Customer Name">
    </div>
    <div class="col-md-2">
        <select name="status" id="filterStatus" class="form-control">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>
    <div class="col-md-2">
        <input type="date" name="start_date" id="filterStartDate" class="form-control">
    </div>
    <div class="col-md-2">
        <input type="date" name="end_date" id="filterEndDate" class="form-control">
    </div>
    <div class="col-md-2">
        <button type="button" id="searchBtn" class="btn btn-primary w-100">Search</button>
    </div>
</form>

                <div class="table-responsive">
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
                                <th class="">Link/UUID</th>
                                <th class="">Source</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                     <tbody id="tripTableBody">
    @forelse ($bookings as $index => $booking)
        <tr>
            <td>{{ $index + 1 }}</td>
            <!-- <td>{{ $booking->source ?? '—' }}</td> -->
            <td>{{ $booking->customer_name ?? '—' }}</td>
            <td>{{ $booking->booking_status ?? '—' }}</td>
          <td>{{ optional($booking->agent)->first_name }} {{ optional($booking->agent)->last_name }}</td>

            <td>{{ $booking->trip->start_date ?? '—' }}</td>
            <td>{{ $booking->trip->end_date ?? '—' }}</td>
           
         <td>
    <button class="btn btn-sm btn-outline-primary" onclick="copyText({{ $booking->id }})">
        Copy Link
    </button>
    <span id="linkText{{ $booking->id }}" class="d-none">
        {{ route('guest.form', $booking->token) }}?trip_id={{ $booking->trip_id }}
    </span>
</td>

             <td>{{ $booking->source ?? '—' }}</td>
             <td class="text-center">
    <div class="d-flex justify-content-center">
          <!-- View Button -->
           @can('view-trips')
        <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-sm btn-success mx-2">
            View
        </a>
        @endcan
        <!-- Edit Button -->

        @can('edit-trip')
        <a href="{{ route('bookings.edit',$booking->id) }}" class="btn btn-sm btn-primary mx-2"
            data-target="">
            Edit
        </a>
        @endcan
                @can('delete-trip')
                                        <!-- Delete Form -->
                                        <form action="{{ route('bookings.destroy', $booking->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                        @endcan
                                    </div>
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
$('#searchBtn').on('click', function () {
    $.ajax({
        url: "{{ route('bookings.index') }}",
        method: "GET",
        data: $('#filterForm').serialize(),
        success: function (response) {
            $('#tripTableBody').html(response.html);
        },
        error: function () {
            alert('Something went wrong!');
        }
    });
});


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
