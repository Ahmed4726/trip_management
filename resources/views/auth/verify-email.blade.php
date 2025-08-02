<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Email Verification | AdminLTE 3</title>

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
        {{ __('Verify Your Email Address') }}
      </p>

      @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success">
          {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
      @endif

      <p class="mb-3 text-muted">
        {{ __('Thanks for signing up! Please verify your email by clicking the link we just sent to you. If you didn\'t receive the email, we will gladly send you another.') }}
      </p>

      <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <div class="mb-3">
          <button type="submit" class="btn btn-primary btn-block">
            {{ __('Resend Verification Email') }}
          </button>
        </div>
      </form>

      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-secondary btn-block">
          {{ __('Log Out') }}
        </button>
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
