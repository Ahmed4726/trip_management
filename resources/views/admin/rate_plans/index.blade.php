@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container pt-3">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4">Rate Plans</h2>
        <a href="{{ route('rate-plans.create') }}" class="btn btn-primary">Add Rate Plan</a>
    </div>

    @if(session('success'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({ icon: 'success', title: 'Success', text: '{{ session('success') }}', confirmButtonColor: '#3085d6' });
    </script>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light text-uppercase small">
                    <tr>
                        <th>Name</th>
                        <th>Currency</th>
                        <th>Type</th>
                        <th>Tax Included</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($plans as $plan)
                    <tr>
                        <td>{{ $plan->name }}</td>
                        <td>{{ $plan->currency }}</td>
                        <td>{{ ucfirst($plan->base_price_type) }}</td>
                        <td>{{ $plan->tax_included ? 'Yes' : 'No' }}</td>
                        <td>
                            <a href="{{ route('rate-plans.edit', $plan->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('rate-plans.destroy', $plan->id) }}" method="POST" style="display:inline-block;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this plan?')">Delete</button>
                            </form>
                        <a href="{{ route('rate-plan-rules.index', $plan->id) }}" class="btn btn-sm btn-info">Rules</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
@endsection
