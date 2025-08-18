@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
    <div class="container pt-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">Guests Info</h2>
            <!-- <a href="{{ route('trips.create') }}" class="btn btn-primary">Create Trip</a> -->
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
                                <th>Name</th>
                                <th>Email</th>
                                <th>DOB</th>
                                <!-- <th>Passport</th> -->
                                <th>nationality</th>
                                <th>Arrival flight Date</th>
                                <th>Departure flight Date</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($guests as $index => $guest)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $guest->name }}</td>
            <td>{{ $guest->email }}</td>
            <td>{{ $guest->dob }}</td>
            <!-- <td>{{ $guest->passport }}</td> -->
            <td>{{ $guest->nationality }}</td>
            <td>{{ $guest->arrivalFlightDate }}</td>
            <td>{{ $guest->departureFlightDate }}</td>
            <td class="text-center d-flex">
                <a href="{{ route('guest.show',$guest->id) }}" class="btn btn-sm btn-success mx-2">View</a>
               <a href="{{ route('guest.download.pdf',$guest->id) }}" class="btn btn-sm btn-primary">PDF</a>
            </td>
            <!-- <form action="#" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                </form> -->
        </tr>
    @empty
        <tr>
            <td colspan="9" class="text-center">No guests found.</td>
        </tr>
    @endforelse
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
@endsection
