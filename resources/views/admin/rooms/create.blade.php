@extends('layouts.admin')
@section('content')

<div class="content-wrapper">
    <div class="container pt-3">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">Create Room</h2>
           <a href="{{ route('room.index', $boat->id) }}" class="btn btn-secondary">Back</a>

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

                <form action="{{ route('room.store', $boat->id) }}" method="POST">
                    @csrf

                    <!-- Room Name -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Room Name</label>
                            <input type="text" name="room_name" class="form-control" required>
                        </div>

                        <div class="col-md-3">
                            <label>Capacity</label>
                            <input type="number" name="capacity" class="form-control" value="1" min="1">
                        </div>

                        <div class="col-md-3">
                            <label>Price Per Night</label>
                            <input type="number" step="0.01" name="price_per_night" class="form-control">
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="">Select status</option>
                                <option value="available">Available</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn btn-primary">
                        Create Room
                    </button>

                </form>

            </div>
        </div>

    </div>
</div>

@endsection
