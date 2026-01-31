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
