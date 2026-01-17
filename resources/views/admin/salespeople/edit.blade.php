@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
<div class="container pt-3">

<h4 class="mb-3">Edit Salesperson</h4>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.salespeople.update', $salesperson) }}">
@csrf
@method('PUT')

<div class="row">
    <div class="col-md-4 mb-3">
        <label>Name</label>
        <input type="text"
               name="name"
               class="form-control"
               value="{{ $salesperson->name }}"
               required>
    </div>

    <div class="col-md-4 mb-3">
        <label>Email</label>
        <input type="email"
               name="email"
               class="form-control"
               value="{{ $salesperson->email }}">
    </div>

    <div class="col-md-4 mb-3">
        <label>Phone</label>
        <input type="text"
               name="phone"
               class="form-control"
               value="{{ $salesperson->phone }}">
    </div>
</div>

<button class="btn btn-success">Update</button>
<a href="{{ route('admin.salespeople.index') }}" class="btn btn-secondary">Cancel</a>

</form>
</div>
</div>
@endsection
