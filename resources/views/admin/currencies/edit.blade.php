@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container mx-2 pt-3">

<h4 class="mb-3">Edit Currency</h4>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.currencies.update', $currency) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" value="{{ $currency->name }}" required>
    </div>
    <div class="mb-3">
        <label>Symbol</label>
        <input type="text" name="symbol" class="form-control" value="{{ $currency->symbol }}" required>
    </div>
    <div class="mb-3">
        <label>Code</label>
        <input type="text" name="code" class="form-control" value="{{ $currency->code }}" required>
    </div>
    <button type="submit" class="btn btn-primary">Update Currency</button>
    <a href="{{ route('admin.currencies.index') }}" class="btn btn-secondary">Cancel</a>
</form>

</div>
</div>
@endsection
