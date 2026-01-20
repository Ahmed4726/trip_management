@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<div class="container pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4">Create Currency</h2>
        <a href="{{ route('company.index') }}" class="btn btn-secondary">Back</a>
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
            <form action="{{ route('company.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Enter name" required>
                    </div>
                    <div class="col-md-6">
                        <label for="legal_name" class="form-label">Legal Name</label>
                        <input type="text" name="legal_name" id="legal_name" class="form-control" placeholder="Enter legal name">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="slug" class="form-label">Slug (unique)</label>
                        <input type="text" name="slug" id="slug" class="form-control" placeholder="e.g. my-company">
                    </div>
                    <div class="col-md-6">
                        <label for="currency" class="form-label">Currency</label>
                        <input type="text" name="currency" id="currency" class="form-control" placeholder="e.g. USD, IDR">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="timezone" class="form-label">Timezone</label>
                        <input type="text" name="timezone" id="timezone" class="form-control" placeholder="e.g. Asia/Makassar">
                    </div>
                    <div class="col-md-6">
                        <label for="billing_email" class="form-label">Billing Email</label>
                        <input type="email" name="billing_email" id="billing_email" class="form-control" placeholder="Enter billing email">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="address" class="form-label">Address</label>
                        <textarea name="address" id="address" class="form-control" placeholder="Enter address"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="vat_tax_id" class="form-label">VAT / Tax ID</label>
                        <input type="text" name="vat_tax_id" id="vat_tax_id" class="form-control" placeholder="Enter VAT/Tax ID">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Create Currency</button>
            </form>
        </div>
    </div>
</div>
</div>
@endsection
