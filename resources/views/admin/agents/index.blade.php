@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
    <div class="container pt-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">Manage Agents</h2>
            @can('create-agent')
            <a href="{{ route('agents.create') }}" class="btn btn-primary">Create Agent</a>
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
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" id="filterName" class="form-control" placeholder="Search by name">
                    </div>
                    <div class="col-md-4">
                        <input type="text" id="filterEmail" class="form-control" placeholder="Search by email">
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-primary" onclick="fetchAgents()">Filter</button>
                    </div>
                </div>

                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light text-uppercase small">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                 <th>Email</th>
                                 <th>Phone/Whatsapp</th>
                                 <th>Commission/%</th>
                                 <th>Assigned trips</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="agentTableBody">
                            @foreach($agents as $index => $agent)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $agent->first_name }} {{ $agent->last_name }}</td>
                                 <td>{{ $agent->email }}</td>
                                <td>{{ $agent->phone }}</td>
                                <td>{{ $agent->commission }}</td>
                                @php
                                $tripCount = DB::table('agent_trip')->where('agent_id', $agent->id)->count();
                                @endphp
                                <td>
                                    @if($tripCount > 0)
                                        <span>{{ $tripCount }} trip(s) assigned</span>
                                    @else
                                        <span class="text-muted">No trips</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <!-- Assign Trips Button -->
                                    <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#assignTripModal{{ $agent->id }}">
                                        Assign Trips
                                    </button>

                                    @can('edit-agent')
                                    <!-- Trigger Modal -->
                                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal"
                                                data-target="#editUserModal{{ $agent->id }}"
                                                data-id="{{ $agent->id }}"
                                                data-first_name="{{ $agent->first_name }}"
                                                data-last_name="{{ $agent->last_name }}"
                                                data-email="{{ $agent->email }}"
                                                data-commission="{{ $agent->commission }}"
                                                data-phone="{{ $agent->phone }}"
                                                data-company="{{ $agent->company }}"
                                            > Edit
                                            </button>
                                    @endcan
                                    <!-- Delete Form -->
                                    <form action="{{ route('agents.destroy', $agent->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>

                        <!-- Edit Agent Modal -->
                        <div class="modal fade" id="editUserModal{{ $agent->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $agent->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('agents.update', $agent->id) }}" method="POST">
                                        @csrf
                                        @method('POST')

                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editUserModalLabel{{ $agent->id }}">Edit Agent</h5>
                                        </div>

                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label>First Name</label>
                                                <input type="text" name="first_name" class="form-control" value="{{ $agent->first_name }}" required>
                                            </div>

                                            <div class="mb-3">
                                                <label>Last Name</label>
                                                <input type="text" name="last_name" class="form-control" value="{{ $agent->last_name }}" required>
                                            </div>

                                            <div class="mb-3">
                                                <label>Email</label>
                                                <input type="email" name="email" class="form-control" value="{{ $agent->email }}" required>
                                            </div>

                                            <div class="mb-3">
                                                <label>Phone / WhatsApp</label>
                                                <input type="text" name="phone" class="form-control" value="{{ $agent->phone }}" required>
                                            </div>

                                            <div class="mb-3">
                                                <label>Commission (%)</label>
                                                <input type="number" name="commission" class="form-control" value="{{ $agent->commission }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Company name</label>
                                                <input type="text" name="company" class="form-control" value="{{ $agent->company }}" required>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">Update</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Assign Trips Modal -->
                        <div class="modal fade" id="assignTripModal{{ $agent->id }}" tabindex="-1" aria-labelledby="assignTripModalLabel{{ $agent->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('agents.assignTrips', $agent->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="assignTripModalLabel{{ $agent->id }}">Assign Trips to {{ $agent->first_name }}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>

                                        <div class="modal-body">
                                            @foreach($allTrips as $trip)
                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        name="trips[]"
                                                        value="{{ $trip->id }}"
                                                        id="trip{{ $trip->id }}_agent{{ $agent->id }}"
                                                        {{ $agent->trip->contains($trip->id) ? 'checked' : '' }}
                                                    >
                                                    <label class="form-check-label" for="trip{{ $trip->id }}_agent{{ $agent->id }}">
                                                        {{ $trip->title }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">Save</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>


                        @endforeach
                        </tbody>

                    </table>


                    <!-- /.modal -->

                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<script>
    function fetchAgents() {
    $.ajax({
        url: "{{ route('agents.filter') }}",
        method: "GET",
        data: {
            name: $('#filterName').val(),
            email: $('#filterEmail').val()
        },
        success: function (response) {
            $('#agentTableBody').html(response.html);
        },
        error: function () {
            alert('Failed to fetch agents.');
        }
    });
}
$(document).ready(function() {
  $('#editUserModal{{ $agent->id }}').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);

    // Get data attributes from the Edit button
    var id = button.data('id');
    var firstName = button.data('first_name');
    var lastName = button.data('last_name');
    var email = button.data('email');
    var phone = button.data('phone');
    var commission = button.data('commission');

    // Fill the form inside the modal
    var modal = $(this);
    modal.find('input[name="first_name"]').val(firstName);
    modal.find('input[name="last_name"]').val(lastName);
    modal.find('input[name="email"]').val(email);
    modal.find('input[name="phone"]').val(phone);
    modal.find('input[name="commission"]').val(commission);

    // Set form action
    modal.find('form').attr('action', '/agents/' + id);
  });
});



setTimeout(function() {
        let message = document.getElementById('success-message');
        if (message) {
            message.style.display = 'none';
        }
    }, 2000); // 3000 milliseconds = 3 seconds
</script>


@endsection
