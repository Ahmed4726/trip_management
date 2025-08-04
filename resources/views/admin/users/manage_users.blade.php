@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
    <div class="container pt-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">Manage Users</h2>
            <a href="/create-user" class="btn btn-primary">Create User</a>
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
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light text-uppercase small">
                            <tr>
                                <th>#</th>
                                <th>First name</th>
                                 <th>Last name</th>
                                  <th class="">Email</th>
                                   <th>Password</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
@foreach($users as $index => $user)
<tr>
    <td>{{ $index + 1 }}</td>
    <td>{{ $user->first_name }}</td>
    <td>{{ $user->last_name }}</td>
    <td>{{ $user->email }}</td>
    <td>********</td>
    <td class="text-center">
        <!-- Trigger Modal -->
        <button type="button"
        class="btn btn-sm btn-primary"
        data-toggle="modal"
        data-target="#editUserModal{{ $user->id }}"
        data-id="{{ $user->id }}"
        data-first_name="{{ $user->first_name }}"
        data-last_name="{{ $user->last_name }}"
        data-email="{{ $user->email }}">
    Edit
</button>


        <!-- Delete Form -->
        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</button>
        </form>
    </td>
</tr>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('POST')

                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel{{ $user->id }}">Edit User</h5>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label>First Name</label>
                        <input type="text" name="first_name" class="form-control" value="{{ $user->first_name }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Last Name</label>
                        <input type="text" name="last_name" class="form-control" value="{{ $user->last_name }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Password (Leave blank to keep unchanged)</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Role</label>
                        <select name="role" class="form-control">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
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
  $('#editUserModal{{ $user->id }}').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);

    // Get data attributes from the Edit button
    var id = button.data('id');
    var firstName = button.data('first_name');
    var lastName = button.data('last_name');
    var email = button.data('email');

    // Fill the form inside the modal
    var modal = $(this);
    modal.find('#edit-user-first-name').val(firstName);
    modal.find('#edit-user-last-name').val(lastName);
    modal.find('#edit-user-email').val(email);

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
