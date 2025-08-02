@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
    <div class="container pt-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">Manage Trips</h2>
            <a href="{{ route('trips.create') }}" class="btn btn-primary">Create Trip</a>
        </div>

        @if(session('success'))
        <div class="alert alert-success" id="success-message">
            {{ session('success') }}
        </div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light text-uppercase small">
                            <tr>
                                <th>#</th>
                                <th>Boat</th>
                                <th>Guests</th>
                                <th>Agent Name</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Price</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trips as $index => $trip)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $trip->boat }}</td>
                                <td>{{ $trip->guests }}</td>
                                <td>{{ $trip->agent ? $trip->agent->first_name . ' ' . $trip->agent->last_name : '-' }}</td>
                                <td>{{ $trip->start_date }}</td>
                                <td>{{ $trip->end_date }}</td>
                                <td>${{ $trip->price }}</td>
                                <td class="text-center">
                                    <!-- Trigger Modal -->
                                    <button type="button"
                                        class="btn btn-sm btn-primary"
                                        data-toggle="modal"
                                        data-target="#editTripModal{{ $trip->id }}">
                                        Edit
                                    </button>

                                    <!-- Delete Form -->
                                    <form action="{{ route('trips.destroy', $trip->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Trip Modal -->
                            <div class="modal fade" id="editTripModal{{ $trip->id }}" tabindex="-1" aria-labelledby="editTripModalLabel{{ $trip->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('trips.update', $trip->id) }}" method="POST">
                                            @csrf
                                            @method('POST')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Trip</h5>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label>Boat</label>
                                                    <input type="text" name="boat" class="form-control" value="{{ $trip->boat }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Guests</label>
                                                    <input type="number" name="guests" class="form-control" value="{{ $trip->guests }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Agent</label>
                                                    <select name="agent_id" class="form-control" required>
                                                        @foreach($agents as $agent)
                                                            <option value="{{ $agent->id }}" {{ $trip->agent_id == $agent->id ? 'selected' : '' }}>
                                                                {{ $agent->first_name }} {{ $agent->last_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Start Date</label>
                                                    <input type="date" name="start_date" class="form-control" value="{{ $trip->start_date }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>End Date</label>
                                                    <input type="date" name="end_date" class="form-control" value="{{ $trip->end_date }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Price</label>
                                                    <input type="number" step="0.01" name="price" class="form-control" value="{{ $trip->price }}" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success">Update</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<script>
    $(document).ready(function() {
  $('#editTripModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);

    // Get data attributes from the Edit button
    var id = button.data('id');
    var boat = button.data('boat');
    var guests = button.data('guests');
    var price = button.data('price');
    var startDate = button.data('start_date');
    var endDate = button.data('end_date');
    var agentId = button.data('agent_id');

    // Fill the form inside the modal
    var modal = $(this);
    modal.find('#edit-trip-boat').val(boat);
    modal.find('#edit-trip-guests').val(guests);
    modal.find('#edit-trip-price').val(price);
    modal.find('#edit-trip-start-date').val(startDate);
    modal.find('#edit-trip-end-date').val(endDate);
    modal.find('#edit-trip-agent-id').val(agentId);

    // Set form action
    modal.find('#editTripForm').attr('action', '/trips/' + id);
  });
});
    setTimeout(function () {
        let message = document.getElementById('success-message');
        if (message) {
            message.style.display = 'none';
        }
    }, 2000);
</script>
@endsection
