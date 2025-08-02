@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
    <div class="container pt-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">Manage Agents</h2>
            <a href="{{ route('agents.create') }}" class="btn btn-primary">Create Agent</a>
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
                                <th>First name</th>
                                 <th>Last name</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($agents as $index => $agent)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $agent->first_name }}</td>
                                <td>{{ $agent->last_name }}</td>
                            
                                <td class="text-center">
                                    <!-- Trigger Modal -->
                                    <button type="button"
                                    class="btn btn-sm btn-primary"
                                    data-toggle="modal"
                                    data-target="#editUserModal{{ $agent->id }}"
                                    data-id="{{ $agent->id }}"
                                    data-first_name="{{ $agent->first_name }}"
                                    data-last_name="{{ $agent->last_name }}"
                                    >
                                Edit
                            </button>


        <!-- Delete Form -->
        <form action="{{ route('agents.destroy', $agent->id) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</button>
        </form>
    </td>
</tr>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal{{ $agent->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $agent->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('agents.update', $agent->id) }}" method="POST">
                @csrf
                @method('POST')

                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel{{ $agent->id }}">Edit Agent</h5>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
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

                   
                    <!-- /.modal -->

                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<script>
$(document).ready(function() {
  $('#editUserModal{{ $agent->id }}').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    
    // Get data attributes from the Edit button
    var id = button.data('id');
    var firstName = button.data('first_name');
    var lastName = button.data('last_name');


    // Fill the form inside the modal
    var modal = $(this);
    modal.find('#edit-user-first-name').val(firstName);
    modal.find('#edit-user-last-name').val(lastName);
   
    
    // Set form action
    modal.find('#editUserForm').attr('action', '/users/' + id);
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
