@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container pt-3">

<h2>Cancellation Policies</h2>
<a href="{{ route('cancellation-policies.create') }}" class="btn btn-primary mb-3">Add Cancellation Policy</a>

@if(session('success'))
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>Swal.fire({icon:'success',title:'Success',text:'{{ session('success') }}',confirmButtonColor:'#3085d6'});</script>
@endif

<table class="table table-bordered table-striped align-middle">
<thead class="table-light text-uppercase small">
<tr>
<th>Name</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
@foreach($policies as $policy)
<tr>
<td>{{ $policy->name }}</td>
<td>
<a href="{{ route('cancellation-policies.edit', $policy->id) }}" class="btn btn-sm btn-warning">Edit</a>
<form action="{{ route('cancellation-policies.destroy', $policy->id) }}" method="POST" style="display:inline-block;">
@csrf @method('DELETE')
<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this policy?')">Delete</button>
</form>
<a href="{{ route('cancellation-policy-rules.index', $policy->id) }}" class="btn btn-sm btn-info">Rules</a>
</td>
</tr>
@endforeach
</tbody>
</table>

</div>
</div>
@endsection
