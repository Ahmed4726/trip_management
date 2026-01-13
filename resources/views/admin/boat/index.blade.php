@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="container pt-3">

        <div class="d-flex justify-content-between mb-3">
            <h4>Boats</h4>
            <a href="{{ route('admin.boats.create') }}" class="btn btn-primary">
                Add Boat
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
                    <th>Rooms</th>
                    <th>Max Capacity</th>
                    <th width="200">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($boats as $boat)
                    <tr>
                        <td>{{ $boat->name }}</td>
                        <td>{{ $boat->rooms_count }}</td>
                        <td>{{ $boat->max_capacity }}</td>
                        <td>
                            <a href="{{ route('admin.boats.edit',$boat) }}"
                               class="btn btn-sm btn-warning">Edit</a>

                            <a href="{{ route('admin.rooms.index',['boat_id'=>$boat->id]) }}"
                               class="btn btn-sm btn-info">Rooms</a>

                            <form method="POST"
                                  action="{{ route('admin.boats.destroy',$boat) }}"
                                  class="d-inline"
                                  onsubmit="return confirm('Delete this boat?')">
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

    </div>
</div>
@endsection
