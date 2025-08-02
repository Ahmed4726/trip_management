@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
    <div class="container pt-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">Roles</h2>
            <a href="{{ route('roles.create') }}" class="btn btn-primary">Create</a>
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
                                <th>Role Name</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $index => $role)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td class="text-center">
                                        <!-- Edit Button -->
                                        <button
                                            class="btn btn-warning btn-sm text-white"
                                            data-toggle="modal"
                                            data-target="#editRoleModal"
                                            data-id="{{ $role->id }}"
                                            data-name="{{ $role->name }}"
                                        >
                                            Edit
                                        </button>

                                        <!-- Delete Form -->
                                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this role?')">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Edit Role Modal -->
                    <div class="modal fade" id="editRoleModal" tabindex="-1" role="dialog" aria-labelledby="editRoleModalLabel" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <form method="POST" id="editRoleForm">
                          @csrf
                          @method('PATCH')
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="editRoleModalLabel">Edit Role</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body">
                              <div class="form-group">
                                <label for="edit-role-name">Role Name</label>
                                <input type="text" name="name" class="form-control" id="edit-role-name" required>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                              <button type="submit" class="btn btn-primary">Update Role</button>
                            </div>
                          </div>
                        </form>
                      </div>
                    </div>
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
  $('#editRoleModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var id = button.data('id');
    var name = button.data('name');
    var modal = $(this);
    modal.find('#edit-role-name').val(name);
    modal.find('#editRoleForm').attr('action', '/roles/' + id);
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
