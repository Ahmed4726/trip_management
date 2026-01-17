@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
<div class="container pt-3">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Salespeople</h4>
    <a href="{{ route('admin.salespeople.create') }}" class="btn btn-primary btn-sm">
        + Add Salesperson
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
<div class="card-body p-0">
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-light text-uppercase small">
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th width="150">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($salespeople as $sp)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $sp->name }}</td>
                <td>{{ $sp->email }}</td>
                <td>{{ $sp->phone }}</td>
                <td>
                    <a href="{{ route('admin.salespeople.edit', $sp) }}" class="btn btn-sm btn-warning">
                        Edit
                    </a>

                    <form action="{{ route('admin.salespeople.destroy', $sp) }}"
                          method="POST"
                          class="d-inline"
                          onsubmit="return confirm('Delete this salesperson?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted">No salespeople found</td>
            </tr>
        @endforelse
    </tbody>
</table>
</div>
</div>

</div>
</div>
@endsection
