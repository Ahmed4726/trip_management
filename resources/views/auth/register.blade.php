<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register | AdminLTE 3</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
</head>
<body class="hold-transition register-page">
<div class="register-box">
 

  <div class="card card-outline card-primary">
     <div class="card-header text-center">
      <a href="../../index2.html" class="h1"><b>Admin</b>LTE</a>
    </div>
    <div class="card-body register-card-body">
      <p class="login-box-msg">Register a new membership</p>

      <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Name --}}
        <div class="input-group mb-3">
          <input type="text" name="name" class="form-control" placeholder="Full name" value="{{ old('name') }}" required autofocus autocomplete="name">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        @error('name')
          <div class="text-danger text-sm mb-2">{{ $message }}</div>
        @enderror

        {{-- Email --}}
        <div class="input-group mb-3">
          <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}" required autocomplete="username">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        @error('email')
          <div class="text-danger text-sm mb-2">{{ $message }}</div>
        @enderror

        {{-- Password --}}
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password" required autocomplete="new-password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        @error('password')
          <div class="text-danger text-sm mb-2">{{ $message }}</div>
        @enderror

        {{-- Confirm Password --}}
        <div class="input-group mb-3">
          <input type="password" name="password_confirmation" class="form-control" placeholder="Retype password" required autocomplete="new-password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        @error('password_confirmation')
          <div class="text-danger text-sm mb-2">{{ $message }}</div>
        @enderror

        {{-- Terms Checkbox --}}
        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="agreeTerms" name="terms" value="agree">
              <label for="agreeTerms">
                I agree to the <a href="#">terms</a>
              </label>
            </div>
          </div>

          {{-- Register Button --}}
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Register</button>
          </div>
        </div>
      </form>

      <a href="{{ route('login') }}" class="text-center d-block mt-3">I already have a membership</a>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
</body>
</html>
