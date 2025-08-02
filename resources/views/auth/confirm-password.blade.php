<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Confirm Password | AdminLTE 3</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a href="{{ route('login') }}" class="h1"><b>Admin</b>LTE</a>
    </div>
    <div class="card-body login-card-body">
      <p class="login-box-msg">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
      </p>

      <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        {{-- Password --}}
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="{{ __('Password') }}" required autocomplete="current-password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        @error('password')
          <div class="text-danger text-sm mb-3">{{ $message }}</div>
        @enderror

        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">{{ __('Confirm') }}</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
</body>
</html>
