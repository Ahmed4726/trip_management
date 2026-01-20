@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container mx-2 pt-3">

<h4 class="mb-3">Add Currency</h4>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.currencies.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Symbol</label>
        <input type="text" name="symbol" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Code</label>
        <input type="text" name="code" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Add Currency</button>
    <a href="{{ route('admin.currencies.index') }}" class="btn btn-secondary">Cancel</a>
</form>

</div>
</div>
@endsection
