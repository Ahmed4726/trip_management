@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="container pt-3">

        <h4>Add Region</h4>

        <form method="POST" action="{{ route('admin.regions.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Region Name</label>
                <input type="text"
                       name="name"
                       class="form-control"
                       required>
            </div>

            <button class="btn btn-success">Save</button>
            <a href="{{ route('admin.regions.index') }}" class="btn btn-secondary">Back</a>
        </form>

    </div>
</div>
@endsection
