@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
    <div class="container pt-3">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">Audit & Logging</h2>
            <a href="{{ route('boat.index') }}" class="btn btn-secondary">Back</a>
        </div>

        <div class="card">
            <div class="card-body">

            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light text-uppercase small">
                        <tr>
                            <th>#</th>
                            <th>Model</th>
                            <th>Record ID</th>
                            <th>Action</th>
                            <th>User</th>
                            <th>Changes</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($audits as $audit)
                        <tr>
                            <td>{{ $audit->id }}</td>
                            <td>{{ class_basename($audit->auditable_type) }}</td>
                            <td>{{ $audit->auditable_id }}</td>
                            <td>
                                @if($audit->action == 'created')
                                    <span class="badge bg-success">Created</span>
                                @elseif($audit->action == 'updated')
                                    <span class="badge bg-primary">Updated</span>
                                @elseif($audit->action == 'deleted')
                                    <span class="badge bg-danger">Deleted</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($audit->action) }}</span>
                                @endif
                            </td>
                            <td>{{ $audit->user?->name ?? 'System' }}</td>
                            <td>
                                @if(is_array($audit->changes))
                                    <ul class="mb-0">
                                        @foreach($audit->changes as $key => $value)
                                            <li><strong>{{ $key }}:</strong> {{ $value }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    {{ $audit->changes }}
                                @endif
                            </td>
                            <td>{{ $audit->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No audit logs found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $audits->links() }} <!-- Pagination links -->
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
