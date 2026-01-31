@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
<div class="container pt-3">
    <h4>Add Guest</h4>

    <form action="{{ route('admin.guests.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Gender</label>
            <select name="gender" class="form-control" required>
                <option value="">Select</option>
                <option>Male</option>
                <option>Female</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control">
        </div>

        <div class="mb-3">
            <label>Passport</label>
            <input type="text" name="passport" class="form-control">
        </div>

        <div class="mb-3">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control">
        </div>

        <div class="mb-3">
            <label>Address</label>
            <input type="text" name="address" class="form-control">
        </div>

        <div class="mb-3">
            <label>Date of Birth</label>
            <input type="date" name="dob" class="form-control">
        </div>

        <button class="btn btn-success">Save</button>
        <a href="{{ route('admin.guests.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</div>
@endsection
