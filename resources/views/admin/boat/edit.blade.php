@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="container pt-3">

        <div class="d-flex justify-content-between mb-3">
            <h4>Edit Boat</h4>
            <a href="{{ route('admin.boats.index') }}" class="btn btn-secondary">
                Back
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.boats.update', $boat) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Boat Name</label>
                <input type="text"
                       name="name"
                       class="form-control"
                       value="{{ old('name', $boat->name) }}"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Max Capacity</label>
                <input type="number"
                       name="max_capacity"
                       class="form-control"
                       value="{{ old('max_capacity', $boat->max_capacity) }}"
                       min="1"
                       required>
            </div>

            <button class="btn btn-success">Update</button>
            <a href="{{ route('admin.boats.index') }}" class="btn btn-secondary">
                Cancel
            </a>
        </form>

    </div>
</div>
@endsection
