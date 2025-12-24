@extends('layouts.admin')
@section('content')

<div class="content-wrapper">
    <div class="container pt-3">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">Create Boat</h2>
            <a href="{{ route('boat.index') }}" class="btn btn-secondary">Back</a>
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

                <form action="{{ route('boat.store') }}" method="POST">
                    @csrf

                    <!-- Boat Name -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Boat Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label>Location</label>
                            <input type="text" name="location" class="form-control" required>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="">Select status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn btn-primary">
                        Create Boat
                    </button>

                </form>

            </div>
        </div>

    </div>
</div>

@endsection
