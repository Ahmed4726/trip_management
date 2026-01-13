@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="container pt-3">

        <div class="d-flex justify-content-between mb-3">
            <h4>Edit Region</h4>
            <a href="{{ route('admin.regions.index') }}" class="btn btn-secondary">
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

        <form method="POST" action="{{ route('admin.regions.update', $region) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Region Name</label>
                <input
                    type="text"
                    name="name"
                    class="form-control"
                    value="{{ old('name', $region->name) }}"
                    required
                >
            </div>

            <button class="btn btn-success">Update</button>
            <a href="{{ route('admin.regions.index') }}" class="btn btn-secondary">Cancel</a>
        </form>

    </div>
</div>
@endsection
