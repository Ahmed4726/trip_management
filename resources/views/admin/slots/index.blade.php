@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container pt-3">

<div class="d-flex justify-content-between mb-3">
    <h4>Slots</h4>
    <a href="{{ route('admin.slots.create') }}" class="btn btn-primary">Create Slot</a>
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
    <th>ID</th>
    <th>Template</th>
    <th>Slot Type</th>
    <th>Status</th>
    <th>Boat</th>
    <th>Region</th>
    <th>Departure → Arrival</th>
    <th>Start → End</th>
    <th>Actions</th>
</tr>
</thead>
<tbody>
@foreach($slots as $slot)
<tr>
    <td>{{ $slot->id }}</td>
    <td>{{ $slot->template ? $slot->template->product_name : '-' }}</td>
    <td>{{ $slot->slot_type }}</td>
    <td>{{ $slot->status }}</td>
    <td>{{ $slot->boat->name ?? '-' }}</td>
    <td>{{ $slot->region->name ?? '-' }}</td>
    <td>{{ $slot->departurePort->name ?? '-' }} → {{ $slot->arrivalPort->name ?? '-' }}</td>
    <td>{{ $slot->start_date->format('Y-m-d') }} → {{ $slot->end_date->format('Y-m-d') }}</td>
    <td>
        <a href="{{ route('admin.slots.edit', $slot) }}" class="btn btn-sm btn-warning">Edit</a>
        <form method="POST" action="{{ route('admin.slots.destroy', $slot) }}" class="d-inline" onsubmit="return confirm('Delete this slot?')">
            @csrf
            @method('DELETE')
            <button class="btn btn-sm btn-danger">Delete</button>
        </form>
    </td>
</tr>
@endforeach
</tbody>
</table>

</div>
</div>
@endsection
