{{-- Blade Template --}}
@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
    <div class="container pt-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">Manage Boats</h2>
            @can('create-boat')
                <a href="{{ route('admin.boat.create') }}" class="btn btn-primary">Create Boat</a>
            @endcan
        </div>

        @foreach (['success','error'] as $msg)
            @if(session($msg))
                <div class="alert alert-{{ $msg == 'success' ? 'success' : 'danger' }}">
                    {{ session($msg) }}
                </div>
            @endif
        @endforeach

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light text-uppercase small">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Rooms</th>
                                <th>Created At</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($boats as $boat)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $boat->name }}</td>
                                    <td>{{ $boat->location }}</td>
                                    <td>
                                        <span class="badge {{ $boat->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ ucfirst($boat->status) }}
                                        </span>
                                    </td>
                                    <td>
                                    <button class="btn btn-sm btn-info view-rooms-btn"
                                            data-boat="{{ $boat->id }}"
                                            data-boat-name="{{ $boat->name }}"
                                            title="View Rooms">
                                        <i class="fas fa-eye"></i>
                                        <span class="badge bg-light text-dark ms-1">{{ $boat->rooms->count() }}</span>
                                    </button>

                                    </td>
                                    <td>{{ $boat->created_at->format('d M Y') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('room.index', $boat->id) }}" class="btn btn-sm btn-info">Rooms</a>

                                        <a href="{{ route('boats.show', $boat->id) }}" class="btn btn-sm btn-primary">
                                            View Details
                                        </a>

                                        <a href="javascript:void(0);" class="btn btn-sm btn-primary edit-boat-btn"
                                        data-id="{{ $boat->id }}"
                                        data-name="{{ $boat->name }}"
                                        data-location="{{ $boat->location }}"
                                        data-status="{{ $boat->status }}">
                                        Edit
                                        </a>

                                        <form action="{{ route('boat.destroy', $boat->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this boat?')">Delete</button>
                                        </form>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No boats available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Edit Boat Modal -->
<div class="modal fade" id="editBoatModal" tabindex="-1" aria-labelledby="editBoatLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="editBoatForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editBoatLabel">Edit Boat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="boat_id" id="boat_id">
                    <div class="mb-3">
                        <label class="form-label">Boat Name</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" id="location" name="location" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Update Boat</button>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- Rooms Modal -->
<div class="modal fade" id="boatRoomsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="boatRoomsTitle">Boat Rooms</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="boatRoomsContent"></div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Edit Boat
    $('.edit-boat-btn').on('click', function() {
        let boat = $(this).data();
        $('#boat_id').val(boat.id);
        $('#name').val(boat.name);
        $('#location').val(boat.location);
        $('#status').val(boat.status);
        $('#editBoatForm').attr('action', '/boats/' + boat.id);
        $('#editBoatModal').modal('show');
    });

    // View Rooms Modal
    $('.view-rooms-btn').on('click', function() {
        let boatId = $(this).data('boat');
        let boatName = $(this).data('boat-name');
        $('#boatRoomsTitle').text(`Rooms of ${boatName}`);

        $.get(`/boat/rooms/${boatId}`, function(data) {

            // alert(data);
            if (!data.rooms) return console.error('No rooms returned', data);

            let html = '<ul class="list-group">';
data.rooms.forEach(r => {
    html += `<li class="list-group-item d-flex flex-column">
                <span>
                    <strong>${r.room_name}</strong> (Capacity: ${r.capacity})
                    <span style="color:${r.is_booked ? 'red' : 'green'}">
                        ${r.is_booked ? 'Booked' : 'Available'}
                    </span>
                </span>`;

    if(r.bookings.length > 0) {
        html += '<small>';
        html += r.bookings.map(b => `<strong>Trip:</strong> ${b.trip_title} | ${b.start_date} to ${b.end_date}`).join('<br>');
        html += '</small>';
    }

    html += `</li>`;
});


            html += '</ul>';

            $('#boatRoomsContent').html(html);
            $('#boatRoomsModal').modal('show');
        });
    });
});
</script>
@endsection
