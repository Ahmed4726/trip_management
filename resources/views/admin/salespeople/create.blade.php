@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
<div class="container pt-3">

<h4 class="mb-3">Add Salesperson</h4>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.salespeople.store') }}">
@csrf

<div class="row">
    <div class="col-md-4 mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="col-md-4 mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control">
    </div>

    <div class="col-md-4 mb-3">
        <label>Phone</label>
        <input type="text" name="phone" class="form-control">
    </div>
</div>

<button class="btn btn-success">Save</button>
<a href="{{ route('admin.salespeople.index') }}" class="btn btn-secondary">Cancel</a>

</form>
</div>
</div>
@endsection
