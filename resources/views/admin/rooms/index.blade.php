@extends('layouts.admin')
@section('content')

<div class="content-wrapper">
    <div class="container pt-3">

        <!-- Header with buttons -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">
                Manage Rooms
                <small class="text-muted">({{ $boat->name }})</small>
            </h2>

            @can('create-room')
                <div class="d-flex gap-2">
                    <a href="{{ route('room.create', $boat->id) }}" class="btn btn-primary">
                        Create Room
                    </a>
                    <a href="{{ route('boat.index') }}" class="btn btn-success mx-2">
                        Back to Boats
                    </a>
                </div>
            @endcan
        </div>

        <!-- Success message -->
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

        <!-- Rooms Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light text-uppercase small">
                            <tr>
                                <th>#</th>
                                <th>Room Name</th>
                                <th>Capacity</th>
                                <th>Price / Night</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($rooms as $room)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $room->room_name }}</td>
                                    <td><span class="badge bg-info">{{ $room->capacity }}</span></td>
                                    <td>{{ $room->price_per_night ? '$' . number_format($room->price_per_night, 2) : '-' }}</td>
                                    <td>
                                        <span class="badge
                                            @if($room->status === 'available') bg-success
                                            @elseif($room->status === 'maintenance') bg-warning
                                            @else bg-secondary @endif">
                                            {{ ucfirst($room->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $room->created_at->format('d M Y') }}</td>
                                    <td class="text-center">

                                        @can('edit-room')
                                            <a href="javascript:void(0);" class="btn btn-sm btn-primary edit-room-btn"
                                               data-id="{{ $room->id }}"
                                               data-room_name="{{ $room->room_name }}"
                                               data-capacity="{{ $room->capacity }}"
                                               data-price_per_night="{{ $room->price_per_night }}"
                                               data-status="{{ $room->status }}">
                                               Edit
                                            </a>
                                        @endcan

                                        @can('delete-room')
                                            <form action="{{ route('room.destroy', [$boat->id, $room->id]) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Delete this room?')">
                                                    Delete
                                                </button>
                                            </form>
                                        @endcan

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        No rooms available for this boat
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Edit Room Modal -->
<div class="modal fade" id="editRoomModal" tabindex="-1" aria-labelledby="editRoomLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="editRoomForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editRoomLabel">Edit Room</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="room_id" id="room_id">

                    <div class="mb-3">
                        <label class="form-label">Room Name</label>
                        <input type="text" id="room_name" name="room_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Capacity</label>
                        <input type="number" id="capacity" name="capacity" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price Per Night</label>
                        <input type="number" step="0.01" id="price_per_night" name="price_per_night" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="available">Available</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Update Room</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- jQuery for modal -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.edit-room-btn').on('click', function() {
        let room = $(this).data();

        $('#room_id').val(room.id);
        $('#room_name').val(room.room_name);
        $('#capacity').val(room.capacity);
        $('#price_per_night').val(room.price_per_night);
        $('#status').val(room.status);

        // Set the form action dynamically
        $('#editRoomForm').attr('action', '/boats/{{ $boat->id }}/rooms/' + room.id);

        $('#editRoomModal').modal('show');
    });
});
</script>

@endsection
