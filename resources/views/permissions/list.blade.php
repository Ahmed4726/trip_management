@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
    <div class="container pt-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">Permissions</h2>
            <a href="{{ route('permissions.create') }}" class="btn btn-primary">Create</a>
        </div>

        {{-- Success Message --}}
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
                                <th>Permission Name</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                           @foreach($permissions as $index => $permission)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $permission->name }}</td>
        <td class="text-center">
<button
  class="btn btn-warning btn-sm text-white"
  data-toggle="modal"
  data-target="#editPermissionModal"
  data-id="{{ $permission->id }}"
  data-name="{{ $permission->name }}"
>
  Edit
</button>


            <!-- Delete Form -->
<form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" class="d-inline">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger btn-sm"
            onclick="return confirm('Are you sure you want to delete this permission?')">
        Delete
    </button>
</form>

        </td>
    </tr>


@endforeach

                        </tbody>
                    </table>
                    <!-- Edit Permission Modal -->
<!-- Modal -->
<div class="modal fade" id="editPermissionModal" tabindex="-1" role="dialog" aria-labelledby="editPermissionModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="POST" id="editPermissionForm">
      @csrf
      @method('PATCH')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editPermissionModalLabel">Edit Permission</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="edit-name">Permission Name</label>
            <input type="text" name="name" class="form-control" id="edit-name" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update Permission</button>
        </div>
      </div>
    </form>
  </div>
</div>


                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<script>
$(document).ready(function() {
  $('#editPermissionModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var id = button.data('id');
    var name = button.data('name');
    var modal = $(this);
    modal.find('#edit-name').val(name);
    modal.find('#editPermissionForm').attr('action', '/permissions/' + id);
  });
});

</script>

@endsection
