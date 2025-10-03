 <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ ('/dashboard') }}" class="brand-link elevation-4">
      <img src="../../dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">Trip management</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
         <i class="fas fa-user-circle fa-2x text-secondary"></i>
        </div>
       <div class="info">
  <a href="/profile" class="d-block">{{ Auth::user()->first_name }} {{ Auth::user()->last_name  }}</a>
</div>

      </div>

      <!-- SidebarSearch Form -->
      <!-- <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div> -->

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
       <li class="nav-item">
    <a href="/dashboard" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>Dashboard</p>
    </a>
</li>
@can('roles-permissions')
<li class="nav-item {{ request()->is('roles*') || request()->is('permissions*') || request()->is('roles-permissions*') ? 'menu-open' : '' }}">
  <a href="#" class="nav-link {{ request()->is('roles*') || request()->is('permissions*') || request()->is('roles-permissions*') ? 'active' : '' }}">
    <i class="nav-icon fas fa-lock"></i>
    <p>
      Roles & Permissions
      <i class="right fas fa-angle-left"></i>
    </p>
  </a>

  <ul class="nav nav-treeview">
    <li class="nav-item">
      <a href="/roles" class="nav-link {{ request()->is('roles') ? 'active' : '' }}">
        <i class="far fa-circle nav-icon"></i>
        <p>Roles</p>
      </a>
    </li>

    <li class="nav-item">
      <a href="{{ route('permissions.index') }}" class="nav-link {{ request()->is('permissions') ? 'active' : '' }}">
        <i class="far fa-circle nav-icon"></i>
        <p>Permissions</p>
      </a>
    </li>

    <li class="nav-item">
      <a href="/roles-permissions" class="nav-link {{ request()->is('roles-permissions') ? 'active' : '' }}">
        <i class="far fa-circle nav-icon"></i>
        <p>Roles & Permissions</p>
      </a>
    </li>
  </ul>
</li>
@endcan

@can('Users')
       <li class="nav-item">
    <a href="/users" class="nav-link {{ request()->is('users','create-user') ? 'active' : '' }}">
        <i class="nav-icon fas fa-users"></i>
        <p>Manage Users</p>
    </a>
</li>
@endcan

@can('trips')
       <!-- Availability / Trips -->
                <li class="nav-item {{ request()->is('trips*') || request()->is('availabilities*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('trips*') || request()->is('availabilities*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-plane"></i>
                        <p>
                            Availability
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('trips.index') }}" class="nav-link {{ request()->is('trips') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Availability</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('rate-plans.index') }}" class="nav-link {{ request()->is('rate-plans*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Rate Plans</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('payment-policies.index') }}" class="nav-link {{ request()->is('payment-policies*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Payment Policies</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('cancellation-policies.index') }}" class="nav-link {{ request()->is('cancellation-policies*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Cancellation Policies</p>
                            </a>
                        </li>
                    </ul>
                </li>
@endcan

@can('booking')
      <li class="nav-item">
    <a href="/bookings" class="nav-link {{ request()->is('bookings','create-booking','booking/*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-clipboard"></i>
        <p>Bookings</p>
    </a>
</li>
@endcan

@can('waitinglist')
    <li class="nav-item">
        <a href="{{ route('admin.waitinglists.index') }}" 
           class="nav-link {{ request()->is('admin/waiting-lists*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-clock"></i>
            <p>Waiting List</p>
        </a>
    </li>
@endcan


@can('agents')
  <li class="nav-item">
    <a href="/agents" class="nav-link {{ request()->is('agents','create-agent') ? 'active' : '' }}">
        <i class="nav-icon fas fa-user-tie"></i>
        <p>Agents</p>
    </a>
  </li>
@endcan

@can('company')
  <li class="nav-item">
    <a href="/companies" class="nav-link {{ request()->is('companies','create-company','company/*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-building"></i>
        <p>Companies</p>
    </a>
  </li>
@endcan

@can('guests')
   <li class="nav-item">
    <a href="/guests" class="nav-link {{ request()->is('guests','guest/*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-user-friends"></i>
        <p>Guests</p>
    </a>
</li>
@endcan

@can('finance')
       <li class="nav-item">
    <a href="/finances" class="nav-link {{ request()->is('finances') ? 'active' : '' }}">
        <i class="nav-icon fas fa-credit-card"></i>
        <p>Finances</p>
    </a>
</li>
@endcan

@can('pdf-approvals')
       <li class="nav-item">
    <a href="#" class="nav-link {{ request()->is('') ? 'active' : '' }}">
        <i class="nav-icon fas fa-file-pdf"></i>
        <p>PDF approvals</p>
    </a>
</li>

@endcan
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>