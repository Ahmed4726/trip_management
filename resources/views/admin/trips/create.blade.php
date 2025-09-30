@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4">Create Availability</h2>
        <a href="{{ route('trips.index') }}" class="btn btn-secondary">Back</a>
    </div>

    @if(session('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        </script>
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
                        <label for="region" class="form-label">Sailing Region</label>
                        <input type="text" name="region" id="region" class="form-control" placeholder="Enter region" required>
                    </div>

                    <div class="col-md-6">
                        <label for="trip_type" class="form-label">Trip type</label>
                        <select name="trip_type" id="trip_type" class="form-control" required>
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
                            <option value="Maintenance">Maintenance</option>
                            <option value="Docking">Docking</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="boat" class="form-label">Boat</label>
                        <select name="boat" id="boat" class="form-control" required>
                            <option value="">Select boat</option>
                            <option value="Samara 1 (5 rooms)">Samara 1 (5 rooms)</option>
                            <option value="Samara 1 (4 rooms)">Samara 1 (4 rooms)</option>
                            <option value="Mischief (5 rooms)">Mischief (5 rooms)</option>
                            <option value="Samara (6 rooms)">Samara (6 rooms)</option>
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
                        <label for="guests" class="form-label">No Of Guests</label>
                        <input type="number" name="guests" id="guests" class="form-control" placeholder="Enter number of guests" required>
                    </div>

                    <div class="col-md-6">
                        <label for="price" class="form-label">Published Rate</label>
                        <input type="text" name="price" id="price" class="form-control" placeholder="Enter published rate" required>
                    </div>

                    <div class="col-md-6">
                        <label for="rate_plan_id" class="form-label">Rate Plan</label>
                        <select name="rate_plan_id" id="rate_plan_id" class="form-control" required>
                            <option value="">Select Rate Plan</option>
                            @foreach($ratePlans as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }} ({{ $plan->currency }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="payment_policy_id" class="form-label">Payment Policy</label>
                        <select name="payment_policy_id" id="payment_policy_id" class="form-control" required>
                            <option value="">Select Payment Policy</option>
                            @foreach($paymentPolicies as $policy)
                                <option value="{{ $policy->id }}">{{ $policy->name }} (DP: {{ $policy->dp_percent }}%)</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="cancellation_policy_id" class="form-label">Cancellation Policy</label>
                        <select name="cancellation_policy_id" id="cancellation_policy_id" class="form-control" required>
                            <option value="">Select Cancellation Policy</option>
                            @foreach($cancellationPolicies as $policy)
                                <option value="{{ $policy->id }}">{{ $policy->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Enter notes"></textarea>
                    </div>
                    @if(auth()->user()->hasRole('admin'))
                    <div class="col-md-6">
                        <label for="company_id" class="form-label">Company Name</label>
                        <select name="company_id" id="company_id" class="form-control" required>
                            <option value="">Select Company</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @else
                        <input type="hidden" name="company_id" value="{{ $company_id }}">
                    @endif

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
