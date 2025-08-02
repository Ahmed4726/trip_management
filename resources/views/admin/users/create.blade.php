@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4">Create User</h2>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Back</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" name="first_name" id="first_name" class="form-control" placeholder="Enter first name" required>
                    </div>
                    <div class="col-md-6">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Enter last name" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="Enter email" required>
                    </div>
                    <div class="col-md-6">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select name="role" id="role" class="form-control" required>
                        <option value="" disabled selected>Select role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Create User</button>
            </form>
        </div>
    </div>
</div>
</div>
@endsection
