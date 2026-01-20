@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="container pt-3">

        <div class="d-flex justify-content-between mb-3">
            <h4>Rooms — {{ $boat->name }}</h4>
            <a href="{{ route('admin.rooms.create',['boat_id'=>$boat->id]) }}"
               class="btn btn-primary">
                Add Room
            </a>
        </div>

        @foreach (['success','error'] as $msg)
            @if(session($msg))
                <div class="alert alert-{{ $msg == 'success' ? 'success' : 'danger' }}">
                    {{ session($msg) }}
                </div>
            @endif
        @endforeach

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Deck</th>
                    <th>Bed Type</th>
                    <th>Capacity</th>
                    <th>Extra Beds</th>
                    <th width="160">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rooms as $room)
                    <tr>
                        <td>{{ $room->room_name }}</td>
                        <td>{{ $room->deck }}</td>
                        <td>{{ $room->bed_type }}</td>
                        <td>{{ $room->capacity }}</td>
                        <td>{{ $room->extra_beds }}</td>
                        <td>
                            <a href="{{ route('admin.rooms.edit',$room) }}"
                               class="btn btn-sm btn-warning">Edit</a>

                            <form method="POST"
                                  action="{{ route('admin.rooms.destroy',$room) }}"
                                  class="d-inline"
                                  onsubmit="return confirm('Delete this room?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <a href="{{ route('admin.boats.index') }}" class="btn btn-secondary mt-3">
            Back to Boats
        </a>

    </div>
</div>
@endsection
