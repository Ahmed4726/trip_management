@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="container pt-3">
        <h2 class="mb-4">Roles & Permissions</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="accordion" id="rolesAccordion">
            @foreach($roles as $role)
                <div class="card">
                    <div class="card-header" id="heading-{{ $role->id }}">
                        <h5 class="mb-0">
                            <button class="btn btn-link" data-toggle="collapse" data-target="#collapse-{{ $role->id }}">
                                {{ $role->name }}
                            </button>
                        </h5>
                    </div>

                    <div id="collapse-{{ $role->id }}" class="collapse" data-parent="#rolesAccordion">
                        <div class="card-body">
                            <form method="POST" action="{{ route('roles.permissions.update', $role->id) }}">
                                @csrf

                                <div class="form-group">
                                    @foreach($permissions as $permission)
                                        <div class="form-check">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                name="permissions[]"
                                                value="{{ $permission->name }}"
                                                id="perm-{{ $role->id }}-{{ $permission->id }}"
                                                {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}
                                            >
                                            <label class="form-check-label" for="perm-{{ $role->id }}-{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>

                                <button type="submit" class="btn btn-sm btn-primary">Update Permissions</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
