@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
<div class="container pt-3">
    <div class="d-flex justify-content-between mb-3">
        <h4>Guests</h4>
        <a href="{{ route('admin.guests.create') }}" class="btn btn-primary">Add Guest</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Gender</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Passport</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($guests as $guest)
                <tr>
                    <td>{{ $guest->id }}</td>
                    <td>{{ $guest->name }}</td>
                    <td>{{ $guest->gender }}</td>
                    <td>{{ $guest->email ?? '-' }}</td>
                    <td>{{ $guest->phone ?? '-' }}</td>
                    <td>{{ $guest->passport ?? '-' }}</td>
                    <td>
                        <a href="{{ route('admin.guests.edit', $guest->id) }}"
                           class="btn btn-sm btn-warning">
                            Edit
                        </a>
                        <form action="{{ route('admin.guests.destroy', $guest->id) }}"
                            method="POST"
                            class="d-inline"
                            onsubmit="return confirm('Are you sure you want to delete this guest?')">
                            @csrf
                            @method('DELETE')

                            <button class="btn btn-sm btn-danger">
                                Delete
                            </button>
                        </form>

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">
                        No guests found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $guests->links() }}
</div>
</div>
@endsection
