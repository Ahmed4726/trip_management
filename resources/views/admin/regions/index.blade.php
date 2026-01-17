@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="container pt-3">

        <div class="d-flex justify-content-between mb-3">
            <h4>Regions</h4>
            <a href="{{ route('admin.regions.create') }}" class="btn btn-primary">
                Add Region
            </a>
        </div>

        @foreach (['success','error'] as $msg)
            @if(session($msg))
                <div class="alert alert-{{ $msg == 'success' ? 'success' : 'danger' }}">
                    {{ session($msg) }}
                </div>
            @endif
        @endforeach

        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light text-uppercase small">
                <tr>
                    <th>Name</th>
                    <th width="160">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($regions as $region)
                    <tr>
                        <td>{{ $region->name }}</td>
                        <td>
                            <a href="{{ route('admin.regions.edit',$region) }}"
                               class="btn btn-sm btn-warning">Edit</a>

                            <form method="POST"
                                  action="{{ route('admin.regions.destroy',$region) }}"
                                  class="d-inline"
                                  onsubmit="return confirm('Delete this region?')">
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
