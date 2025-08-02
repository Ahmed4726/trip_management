 @extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<section class="container mt-5">
    <header class="mb-4">
        <h2 class="h4 text-dark">
            {{ __('Delete Account') }}
        </h2>
        <p class="text-muted">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <!-- Delete Button (triggers modal) -->
    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmDeleteModal">
        {{ __('Delete Account') }}
    </button>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form method="POST" action="{{ route('profile.destroy') }}" class="modal-content">
                @csrf
                @method('DELETE')

                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteLabel">{{ __('Are you sure you want to delete your account?') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <p class="text-muted">
                        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                    </p>

                    <div class="form-group mt-3">
                        <label for="delete_password" class="sr-only">{{ __('Password') }}</label>
                        <input type="password"
                               name="password"
                               id="delete_password"
                               class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                               placeholder="{{ __('Password') }}"
                               required>
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="btn btn-danger">
                        {{ __('Delete Account') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
</div>
@endsection