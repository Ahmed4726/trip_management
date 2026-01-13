@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="container pt-3">

        <h4>Add Room — {{ $boat->name }}</h4>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.rooms.store') }}">
            @csrf

            <input type="hidden" name="boat_id" value="{{ $boat->id }}">

            <div class="mb-3">
                <label class="form-label">Room Name</label>
                <input type="text"
                       name="name"
                       class="form-control"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Deck</label>
                <input type="text"
                       name="deck"
                       class="form-control"
                       placeholder="Upper / Main / Lower">
            </div>

            <div class="mb-3">
                <label class="form-label">Bed Type</label>
                <input type="text"
                       name="bed_type"
                       class="form-control"
                       placeholder="Double / Single / Flexible">
            </div>

            <div class="mb-3">
                <label class="form-label">Extra Beds</label>
                <input type="number"
                       name="extra_beds"
                       class="form-control"
                       value="0"
                       min="0">
            </div>

            <button class="btn btn-success">Save</button>
            <a href="{{ route('admin.rooms.index',['boat_id'=>$boat->id]) }}"
               class="btn btn-secondary">
                Cancel
            </a>
        </form>

    </div>
</div>
@endsection
