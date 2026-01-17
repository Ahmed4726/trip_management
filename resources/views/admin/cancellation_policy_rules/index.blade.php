@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container pt-3">

<h2>Rules for {{ $policy->name }}</h2>
<a href="{{ route('cancellation-policy-rules.create', $policy->id) }}" class="btn btn-primary mb-3">Add Rule</a>

@if(session('success'))
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>Swal.fire({icon:'success',title:'Success',text:'{{ session('success') }}',confirmButtonColor:'#3085d6'});</script>
@endif

<table class="table table-bordered table-striped align-middle">
<thead class="table-light text-uppercase small">
<tr>
<th>Days From</th>
<th>Days To</th>
<th>Penalty %</th>
<th>Refundable</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
@foreach($policy->rules as $rule)
<tr>
<td>{{ $rule->days_from }}</td>
<td>{{ $rule->days_to }}</td>
<td>{{ $rule->penalty_percent }}%</td>
<td>{{ $rule->refundable ? 'Yes' : 'No' }}</td>
<td>
<a href="{{ route('cancellation-policy-rules.edit', [$policy->id,$rule->id]) }}" class="btn btn-sm btn-warning">Edit</a>
<form action="{{ route('cancellation-policy-rules.destroy', [$policy->id,$rule->id]) }}" method="POST" style="display:inline-block;">
@csrf @method('DELETE')
<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete rule?')">Delete</button>
</form>
</td>
</tr>
@endforeach
</tbody>
</table>

</div>
</div>
@endsection
