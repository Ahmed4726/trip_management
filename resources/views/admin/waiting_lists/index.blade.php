@extends('layouts.admin')

@section('content')

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
<div class="content-wrapper">
        <div class="container pt-3">
    <h1>Waiting List</h1>

    {{-- Filters --}}
    <form method="GET" class="row g-3 mb-3">
        <div class="col-md-3">
            <input type="text" name="company_id" value="{{ request('company_id') }}" class="form-control" placeholder="Company ID">
        </div>
        <div class="col-md-3">
            <input type="text" name="availability_id" value="{{ request('availability_id') }}" class="form-control" placeholder="Availability ID">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-control">
                <option value="">-- Status --</option>
                @foreach(['open','notified','converted','cancelled'] as $status)
                    <option value="{{ $status }}" @selected(request('status')==$status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary">Filter</button>
        </div>
    </form>

    {{-- Table --}}
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th><th>Name</th><th>Email</th><th>Party Size</th>
                <th>Availability</th><th>Status</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($waitingLists as $w)
            <tr>
                <td>{{ $w->id }}</td>
                <td>{{ $w->name }}</td>
                <td>{{ $w->email }}</td>
                <td>{{ $w->party_size }}</td>
                <td>{{ $w->availability->title ?? '' }}</td>
                <td><span class="badge bg-info">{{ ucfirst($w->status) }}</span></td>
                <td>
                    @if($w->status=='open')
                        <form action="{{ route('admin.waitinglists.notify',$w) }}" method="POST" style="display:inline">
                            @csrf
                            <button class="btn btn-sm btn-warning">Notify</button>
                        </form>
                    @endif
                    <a href="{{ route('admin.waitinglists.convert',$w) }}" class="btn btn-sm btn-success">Convert</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $waitingLists->links() }}
</div>
</div>
</div>
@endsection
