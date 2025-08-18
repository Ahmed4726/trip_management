@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4">Create Agent</h2>
        <a href="{{ route('agents.index') }}" class="btn btn-secondary">Back</a>
    </div>

        @if(session('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            </script>
        @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('agent.store') }}" method="POST">
                @csrf

               <div class="row mb-3">
    <div class="col-md-6">
        <label for="first_name" class="form-label">First Name</label>
        <input type="text" name="first_name" id="first_name" class="form-control" placeholder="Enter first name" required>
    </div>
    <div class="col-md-6">
        <label for="last_name" class="form-label">Last Name</label>
        <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Enter last name" required>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" id="email" class="form-control" placeholder="Enter email">
    </div>
    <div class="col-md-6">
        <label for="phone" class="form-label">Phone/WhatsApp</label>
        <input type="text" name="phone" id="phone" class="form-control" placeholder="Enter phone or WhatsApp number">
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label for="commission" class="form-label">Commission (%)</label>
        <input type="number" name="commission" id="commission" class="form-control" placeholder="Enter commission percentage">
    </div>
      <div class="col-md-6">
        <label for="company" class="form-label">Company Name</label>
        <input type="text" name="company" id="company" class="form-control" placeholder="Enter company name">
    </div>
</div>


                <button type="submit" class="btn btn-primary">Create Agent</button>
            </form>
        </div>
    </div>
</div>
</div>
@endsection
