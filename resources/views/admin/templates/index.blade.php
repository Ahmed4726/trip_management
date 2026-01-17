@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container pt-3">

<div class="d-flex justify-content-between mb-3">
    <h4>Templates</h4>
    <a href="{{ route('admin.templates.create') }}" class="btn btn-primary">Add Template</a>
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
    <th>Type</th>
    <th>Region</th>
    <th>Vessels Allowed</th>
    <th width="160">Actions</th>
</tr>
</thead>
<tbody>
@foreach($templates as $template)
<tr>
    <td>{{ $template->product_name }}</td>
    <td>{{ $template->product_type }}</td>
    <td>{{ $template->region->name }}</td>
    <td>{{ implode(', ', $template->vessels_allowed_names()) }}</td>
    <td>
        <a href="{{ route('admin.templates.edit',$template) }}" class="btn btn-sm btn-warning">Edit</a>
        <form method="POST" action="{{ route('admin.templates.destroy',$template) }}" class="d-inline" onsubmit="return confirm('Delete this template?')">
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
