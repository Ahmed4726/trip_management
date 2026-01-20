@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container mx-2 pt-3">

<h4 class="mb-3">Currencies</h4>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<a href="{{ route('admin.currencies.create') }}" class="btn btn-primary mb-3">Add Currency</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Symbol</th>
            <th>Code</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($currencies as $currency)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $currency->name }}</td>
            <td>{{ $currency->symbol }}</td>
            <td>{{ $currency->code }}</td>
            <td>
                <a href="{{ route('admin.currencies.edit', $currency) }}" class="btn btn-sm btn-warning">Edit</a>

                <form action="{{ route('admin.currencies.destroy', $currency) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this currency?')">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

</div>
</div>
@endsection
