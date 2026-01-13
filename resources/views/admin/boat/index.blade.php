@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
    <div class="container pt-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">Manage Boats</h2>
            <a href="{{ route('admin.boats.create') }}" class="btn btn-primary">Create Boat</a>
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
                                {{-- <th>Location</th> --}}
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
                                    {{-- <td>{{ $boat->location ?? '-' }}</td> --}}
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
                                            <span class="badge bg-light text-dark ms-1">{{ $boat->rooms_count }}</span>
                                        </button>
                                    </td>
                                    <td>{{ $boat->created_at->format('d M Y') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.rooms.index',['boat_id'=>$boat->id]) }}"
                                           class="btn btn-sm btn-info">Rooms</a>

                                        <a href="{{ route('admin.boats.show', $boat->id) }}" class="btn btn-sm btn-primary">
                                            View Details
                                        </a>

                                        <a href="{{ route('admin.boats.edit', $boat->id) }}" class="btn btn-sm btn-warning">
                                            Edit
                                        </a>

                                        <form action="{{ route('admin.boats.destroy', $boat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this boat?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Delete</button>
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
    // View Rooms Modal
    $('.view-rooms-btn').on('click', function() {
        let boatId = $(this).data('boat');
        let boatName = $(this).data('boat-name');
        $('#boatRoomsTitle').text(`Rooms of ${boatName}`);

        $.get(`/admin/boat/rooms/${boatId}`, function(data) {
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
