@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="container pt-3">

        <h4>Edit Room — {{ $room->boat->name }}</h4>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.rooms.update', $room) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Room Name</label>
                <input type="text"
                       name="name"
                       class="form-control"
                       value="{{ old('name', $room->room_name) }}"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Deck</label>
                <input type="text"
                       name="deck"
                       class="form-control"
                       value="{{ old('deck', $room->deck) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Bed Type</label>
                <input type="text"
                       name="bed_type"
                       class="form-control"
                       value="{{ old('bed_type', $room->bed_type) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Extra Beds</label>
                <input type="number"
                       name="extra_beds"
                       class="form-control"
                       value="{{ old('extra_beds', $room->extra_beds) }}"
                       min="0">
            </div>

            <button class="btn btn-success">Update</button>
            <a href="{{ route('admin.rooms.index',['boat_id'=>$room->boat_id]) }}"
               class="btn btn-secondary">
                Cancel
            </a>
        </form>

    </div>
</div>
@endsection

