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
                        <label for="boat" class="form-label">Boat</label>
                        <input type="text" name="boat" id="boat" class="form-control" placeholder="Enter boat name" required>
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
                </div>

                <button type="submit" class="btn btn-primary">Create Trip</button>
            </form>
        </div>
    </div>
</div>
</div>
@endsection
