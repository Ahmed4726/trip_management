@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4">Create Trip</h2>
        <a href="{{ route('trips.index') }}" class="btn btn-secondary">Back</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('trips.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
    <div class="col-md-6">
        <label for="title" class="form-label">Trip Title</label>
        <input type="text" name="title" id="title" class="form-control" placeholder="Enter trip title" required>
    </div>

    <div class="col-md-6">
        <label for="region" class="form-label">Region</label>
        <input type="text" name="region" id="region" class="form-control" placeholder="Enter region" required>
    </div>

     <div class="col-md-6">
        <label for="status" class="form-label">Trip type</label>
        <select name="trip_type" id="status" class="form-control" required>
            <option value="">Select Type</option>
            <option value="private">Private (1 group charter)</option>
            <option value="open">Open (multiple guests)</option>
        </select>
    </div>
    <div class="col-md-6">
        <label for="status" class="form-label">Status</label>
        <select name="status" id="status" class="form-control" required>
            <option value="">Select status</option>
            <option value="Available">Available</option>
            <option value="On Hold">On Hold</option>
            <option value="Booked">Booked</option>
        </select>
    </div>

 <div class="col-md-6">
    <label for="boat" class="form-label">Boat</label>
    <select name="boat" id="boat" class="form-control" required>
        <option value="">Select boat</option>
        <optgroup label="Samara 1 (5 rooms)">
            <option value="Rinca">Rinca</option>
            <option value="Komodo">Komodo</option>
            <option value="Padar">Padar</option>
            <option value="Kanawa">Kanawa</option>
            <option value="Kelor">Kelor</option>
        </optgroup>
          <optgroup label="Samara 1 (4 rooms)">
            <option value="Room1">Room1</option>
            <option value="Room2">Room2</option>
            <option value="Room3">Room3</option>
            <option value="Room4">Room4</option>
            
        </optgroup>
          <optgroup label="Mischief (5 rooms)">
            <option value="Room1">Room1</option>
            <option value="Room2">Room2</option>
            <option value="Room3">Room3</option>
            <option value="Room4">Room4</option>
            <option value="Room5">Room5</option>
        </optgroup>
          <optgroup label="Samara (6 rooms)">
            <option value="Room1">Room1</option>
            <option value="Room2">Room2</option>
            <option value="Room3">Room3</option>
            <option value="Room4">Room4</option>
            <option value="Room5">Room5</option>
            <option value="Room6">Room6</option>
        </optgroup>
    </select>
</div>



    <div class="col-md-6">
        <label for="start_date" class="form-label">Start Date</label>
        <input type="date" name="start_date" id="start_date" class="form-control" required>
    </div>

    <div class="col-md-6">
        <label for="end_date" class="form-label">End Date</label>
        <input type="date" name="end_date" id="end_date" class="form-control" required>
    </div>

    <div class="col-md-6">
        <label for="guests" class="form-label">Guests</label>
        <input type="number" name="guests" id="guests" class="form-control" placeholder="Enter number of guests" required>
    </div>

    <div class="col-md-6">
        <label for="price" class="form-label">Price</label>
        <input type="text" name="price" id="price" class="form-control" placeholder="Enter price" required>
    </div>

 <div class="col-md-6">
        <label for="leading_guest_id" class="form-label">Leading Guest</label>
        <select name="leading_guest_id" id="leading_guest_id" class="form-control">
            <option value="">Select guest</option>
          
        </select>
    </div>
    <div class="col-md-6">
        <label for="agent_id" class="form-label">Agent</label>
        <select name="agent_id" id="agent_id" class="form-control" required>
            <option value="">Select agent</option>
            @foreach ($agents as $agent)
                <option value="{{ $agent->id }}">
                    {{ $agent->first_name }} {{ $agent->last_name }}
                </option>
            @endforeach
        </select>
    </div>
     <div class="col-md-6">
        <label for="notes" class="form-label">Notes</label>
        <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Enter notes"></textarea>
    </div>
</div>


                <button type="submit" class="btn btn-primary">Create Trip</button>
            </form>
        </div>
    </div>
</div>
</div>
<script>
     document.addEventListener('DOMContentLoaded', function () {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById("start_date").setAttribute("min", today);
        document.getElementById("end_date").setAttribute("min", today);
    });
    document.getElementById("start_date").addEventListener("change", function () {
    const selectedStart = this.value;
    document.getElementById("end_date").setAttribute("min", selectedStart);
});

</script>
@endsection
