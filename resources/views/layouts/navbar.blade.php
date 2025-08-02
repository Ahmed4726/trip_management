  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <!-- <li class="nav-item d-none d-sm-inline-block">
        <a href="../../index3.html" class="nav-link">Home</a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Contact</a>
      </li> -->
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->
      <!-- <li class="nav-item">
        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
          <i class="fas fa-search"></i>
        </a>
        <div class="navbar-search-block">
          <form class="form-inline">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
              <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                  <i class="fas fa-search"></i>
                </button>
                <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
      </li> -->

      <!-- Messages Dropdown Menu -->
      
      <!-- Notifications Dropdown Menu -->
    

<li class="nav-item dropdown">
  <a class="nav-link" data-toggle="dropdown" href="#" role="button">
    <i class="fas fa-user-circle"></i>
  </a>
  <div class="dropdown-menu dropdown-menu-right">
    <a class="dropdown-item" href="{{ route('profile.edit') }}">
      <i class="fas fa-user mr-2"></i> Profile
    </a>
    <a class="dropdown-item" href="/">
      <i class="fas fa-globe mr-2"></i> Go to Website
    </a>
    <div class="dropdown-divider"></div>
    <a class="dropdown-item" href="{{ route('logout') }}"
       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
      <i class="fas fa-sign-out-alt mr-2"></i> Logout
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
      @csrf
    </form>
  </div>
</li>

    </ul>
  </nav>
  <!-- /.navbar -->

 