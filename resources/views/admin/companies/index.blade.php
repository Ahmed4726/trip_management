@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
    <div class="container pt-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">Manage Companies</h2>
            @can('create-agent')
            <a href="{{ route('company.create') }}" class="btn btn-primary">Create Company</a>
            @endcan
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
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light text-uppercase small">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Legal Name</th>
                                <th>Slug</th>
                                <th>Currency</th>
                                <th>Timezone</th>
                                <th>Billing Email</th>
                                <th>VAT/Tax ID</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($companies as $company)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $company->name }}</td>
                                <td>{{ $company->legal_name }}</td>
                                <td>{{ $company->slug }}</td>
                                <td>{{ $company->currency }}</td>
                                <td>{{ $company->timezone }}</td>
                                <td>{{ $company->billing_email }}</td>
                                <td>{{ $company->vat_tax_id }}</td>

                                <td class="text-center">
    <a href="{{ route('company.show', $company->id) }}" class="btn btn-sm btn-success">
        View
    </a>
<a href="https://{{ $company->slug }}.trip_management.test" class="btn btn-sm btn-warning" target="_blank">
    Tenant Link
</a>



    <button class="btn btn-sm btn-primary edit-btn"
        data-id="{{ $company->id }}"
        data-name="{{ $company->name }}"
        data-legal_name="{{ $company->legal_name }}"
        data-slug="{{ $company->slug }}"
        data-currency="{{ $company->currency }}"
        data-timezone="{{ $company->timezone }}"
        data-billing_email="{{ $company->billing_email }}"
        data-address="{{ $company->address }}"
        data-vat_tax_id="{{ $company->vat_tax_id }}">
        Edit
    </button>

    <form action="{{ route('company.destroy', $company->id) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this company?')">Delete</button>
    </form>
</td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">No companies available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Company Modal -->
<div class="modal fade" id="editCompanyModal" tabindex="-1" aria-labelledby="editCompanyLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <form method="POST" id="editCompanyForm">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title" id="editCompanyLabel">Edit Company</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="company_id">

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" id="company_name" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Legal Name</label>
                    <input type="text" id="company_legal_name" name="legal_name" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Slug</label>
                    <input type="text" id="company_slug" name="slug" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Currency</label>
                    <input type="text" id="company_currency" name="currency" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Timezone</label>
                    <input type="text" id="company_timezone" name="timezone" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Billing Email</label>
                    <input type="email" id="company_billing_email" name="billing_email" class="form-control">
                </div>
                 <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" id="company_address" name="address" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">VAT/Tax ID</label>
                    <input type="text" id="company_vat_tax_id" name="vat_tax_id" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
             
                <button type="submit" class="btn btn-success">Update</button>
            </div>
        </form>
    </div>
  </div>
</div>

<!-- Bootstrap JS + jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    $('.edit-btn').on('click', function () {
        let company = $(this).data();

        $('#company_id').val(company.id);
        $('#company_name').val(company.name);
        $('#company_legal_name').val(company.legal_name);
        $('#company_slug').val(company.slug);
        $('#company_currency').val(company.currency);
        $('#company_timezone').val(company.timezone);
        $('#company_billing_email').val(company.billing_email);
        $('#company_address').val(company.address);
        $('#company_vat_tax_id').val(company.vat_tax_id);

        $('#editCompanyForm').attr('action', '/company/' + company.id);

        $('#editCompanyModal').modal('show');
    });
});
</script>
@endsection
