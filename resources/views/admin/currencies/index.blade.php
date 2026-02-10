@extends('layouts.admin')
@section('content')
<!-- At the bottom of your body -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

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
            <th>Rate (USD)</th>
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
            <td>{{ $currency->rate }}</td>
            <td>
                <a href="{{ route('admin.currencies.edit', $currency) }}" class="btn btn-sm btn-warning">Edit</a>

                <!-- Update Rate Button -->
                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#rateModal{{ $currency->id }}">
                    Update Rate
                </button>

                <form action="{{ route('admin.currencies.destroy', $currency) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this currency?')">Delete</button>
                </form>

                <!-- Rate Modal -->
                <div class="modal fade" id="rateModal{{ $currency->id }}" tabindex="-1" aria-labelledby="rateModalLabel{{ $currency->id }}" aria-hidden="true">
                  <div class="modal-dialog">
                    <form action="{{ route('admin.currencies.updateRate', $currency->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="rateModalLabel{{ $currency->id }}">Update Rate for {{ $currency->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <div class="mb-3">
                                <label for="rate{{ $currency->id }}" class="form-label">Rate (USD)</label>
                                <input type="number" step="0.0001" class="form-control" id="rate{{ $currency->id }}" name="rate" value="{{ $currency->rate }}" required>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Rate</button>
                          </div>
                        </div>
                    </form>
                  </div>
                </div>

            </td>
        </tr>
        @endforeach
    </tbody>
</table>

</div>
</div>
@endsection
