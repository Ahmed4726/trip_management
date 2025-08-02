@extends('layouts.admin')

@section('content')

<div class="container mt-5">
    <!-- Page Header -->
    <div class="mb-4">
        <h2 class="h4 text-dark">
            {{ __('Profile') }}
        </h2>
    </div>

    <!-- Update Profile Info -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <!-- Update Password -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <!-- Delete Account -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection
