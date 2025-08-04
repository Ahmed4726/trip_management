@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
    <div class="container pt-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">Manage Trips</h2>
            <a href="{{ route('trips.create') }}" class="btn btn-primary">Create Trip</a>
        </div>

    @if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: (session('success')),
                timer: 3000,
                showConfirmButton: false
            });
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
                                <th>Title</th>
                                <th>Region</th>
                                <th>Status</th>
                                <!-- <th>Trip Type</th> -->
                                <th>Leading Guest</th>
                                <!-- <th>Boat</th>
                                <th>Guests</th> -->
                                <th>Agent Name</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Price</th>
                                <th class="col-2">Link/UUID</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trips as $index => $trip)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $trip->title }}</td>
                                <td>{{ $trip->region }}</td>
                                <td>{{ $trip->status }}</td>
                                <!-- <td>{{ $trip->trip_type }}</td> -->
                                <td>{{ $trip->leading_guest_id }}</td>
                                <!-- <td>{{ $trip->boat }}</td>
                                <td>{{ $trip->guests }}</td> -->
                                <td class="">{{ $trip->agent ? $trip->agent->first_name . ' ' . $trip->agent->last_name : '-' }}</td>
                               <td class="w-25">{{ $trip->start_date }}</td>

                                <td class="w-25">{{ $trip->end_date }}</td>
                                <td>${{ $trip->price }}</td>
<td>
    <button onclick="copyText('{{ $trip->id }}')" class="btn btn-sm btn-outline-primary ">
        Copy Link
    </button>
    <span id="linkText{{ $trip->id }}" class="d-none">{{ $trip->guest_form_url }}</span>
</td>



                      <td class="text-center">
    <div class="d-flex justify-content-center">
          <!-- View Button -->
        <a href="{{ route('trips.show', $trip->id) }}" class="btn btn-sm btn-success mx-2">
            View
        </a>
        <!-- Edit Button -->
        <button type="button"
            class="btn btn-sm btn-primary mx-2"
            data-toggle="modal"
            data-target="#editTripModal{{ $trip->id }}">
            Edit
        </button>

        <!-- Delete Form -->
        <form action="{{ route('trips.destroy', $trip->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</button>
        </form>
    </div>
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
                                                    <label>Title</label>
                                                    <input type="text" name="title" class="form-control" value="{{ $trip->title }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Region</label>
                                                    <input type="text" name="region" class="form-control" value="{{ $trip->region }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label>Status</label>
                                                    <input type="text" name="status" class="form-control" value="{{ $trip->status }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="trip_type">Trip Type</label>
                                                    <select name="trip_type" class="form-control" id="trip_type">
                                                        @foreach ($tripTypes as $type)
                                                    <option value="{{ $type }}" {{ $trip->trip_type == $type ? 'selected' : '' }}>
                                                        {{ $type }}
                                                    </option>
                                                    @endforeach

                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label>Leading Guest ID</label>
                                                    <input type="number" name="leading_guest_id" class="form-control" value="{{ $trip->leading_guest_id }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label>Notes</label>
                                                    <textarea name="notes" class="form-control">{{ $trip->notes }}</textarea>
                                                </div>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        $('.editTripModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);

            // Get data attributes from the Edit button
            var id = button.data('id');
            var title = button.data('title');
            var region = button.data('region');
            var status = button.data('status');
            var tripType = button.data('trip_type');
            var leadingGuestId = button.data('leading_guest_id');
            var notes = button.data('notes');
            var boat = button.data('boat');
            var guests = button.data('guests');
            var price = button.data('price');
            var startDate = button.data('start_date');
            var endDate = button.data('end_date');
            var agentId = button.data('agent_id');

            // Fill the form inside the modal
            var modal = $(this);
            modal.find('[name="title"]').val(title);
            modal.find('[name="region"]').val(region);
            modal.find('[name="status"]').val(status);
            modal.find('[name="trip_type"]').val(tripType);
            modal.find('[name="leading_guest_id"]').val(leadingGuestId);
            modal.find('[name="notes"]').val(notes);
            modal.find('[name="boat"]').val(boat);
            modal.find('[name="guests"]').val(guests);
            modal.find('[name="price"]').val(price);
            modal.find('[name="start_date"]').val(startDate);
            modal.find('[name="end_date"]').val(endDate);
            modal.find('[name="agent_id"]').val(agentId);

            // Set form action
            modal.find('form').attr('action', '/trips/' + id);
        });

        setTimeout(function () {
            let message = document.getElementById('success-message');
            if (message) {
                message.style.display = 'none';
            }
        }, 2000);
    });

        function copyText(id) {
        const span = document.getElementById('linkText' + id);
        const text = span.innerText;

        const temp = document.createElement('textarea');
        temp.value = text;
        document.body.appendChild(temp);
        temp.select();
        document.execCommand('copy');
        document.body.removeChild(temp);

        alert('Link copied!');
    }
</script>


@endsection
