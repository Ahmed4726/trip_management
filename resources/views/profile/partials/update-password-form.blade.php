 @extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<section class="container mt-4">
    <header class="mb-4">
        <h2 class="h4 text-dark">
            {{ __('Update Password') }}
        </h2>
        <p class="text-muted">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <!-- Current Password -->
        <div class="form-group">
            <label for="update_password_current_password">{{ __('Current Password') }}</label>
            <input id="update_password_current_password"
                   name="current_password"
                   type="password"
                   class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                   autocomplete="current-password">
            @error('current_password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- New Password -->
        <div class="form-group">
            <label for="update_password_password">{{ __('New Password') }}</label>
            <input id="update_password_password"
                   name="password"
                   type="password"
                   class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                   autocomplete="new-password">
            @error('password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label for="update_password_password_confirmation">{{ __('Confirm Password') }}</label>
            <input id="update_password_password_confirmation"
                   name="password_confirmation"
                   type="password"
                   class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                   autocomplete="new-password">
            @error('password_confirmation', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Save Button -->
        <div class="form-group d-flex align-items-center gap-2">
            <button type="submit" class="btn btn-primary">
                {{ __('Save') }}
            </button>

            @if (session('status') === 'password-updated')
                <span class="text-success small ml-3">
                    {{ __('Saved.') }}
                </span>
            @endif
        </div>
    </form>
</section>
</div>
@endsection