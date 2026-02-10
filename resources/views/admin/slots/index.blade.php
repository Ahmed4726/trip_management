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

<table class="table table-bordered table-striped table-hover align-middle">
<thead class="table-light text-uppercase small">
<tr>
    <th>Date</th>
    <th>Boat / Region</th>
    <th>Bookings (Confirmed / Pending / Canceled)</th>
    <th>Total Price (USD)</th>
    <th>Actions</th>
</tr>
</thead>
<tbody>
@foreach($slots as $slot)
<tr>
    {{-- Date --}}
    <td>{{ $slot->start_date->format('d M Y') }} → {{ $slot->end_date->format('d M Y') }}</td>

    {{-- Boat / Region --}}
    <td>
        @php
            $boats = collect([]);
            if($slot->boat) $boats->push($slot->boat->name);
            if($slot->boats->count()) $boats = $boats->merge($slot->boats->pluck('name'));
        @endphp
        {{ $boats->join(', ') ?: '-' }}<br>
        <small>{{ $slot->region->name ?? '-' }}</small>
    </td>

    {{-- Bookings counts --}}
    <td>
        @php
            $confirmed = $slot->bookings->where('status','DP Paid')->count();
            $pending = $slot->bookings->where('status','Pending')->count();
            $canceled = $slot->bookings->where('status','Canceled')->count();
        @endphp
        <span class="badge bg-success">{{ $confirmed }}</span>
        <span class="badge bg-warning text-dark">{{ $pending }}</span>
        <span class="badge bg-danger">{{ $canceled }}</span>
    </td>

    {{-- Total Price --}}
    <td>
        ${{ $slot->bookings->sum('price') ?? 0 }}
    </td>

    {{-- Actions --}}
    <td>
        <a href="{{ route('admin.slots.show', $slot) }}" class="btn btn-sm btn-info">View</a>
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
