<div class="navbar-bg"></div>
      <nav class="navbar navbar-expand-lg main-navbar">
        <div class="form-inline mr-auto">
          <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg collapse-btn"><i
                  class="fas fa-bars"></i></a></li>
          </ul>
        </div>
        <ul class="navbar-nav navbar-right">
            <li>
                <div class="text-center mx-2">
                    <div class="bg-danger alertMessage d-none my-1 text-center text-light py-2 px-3" id="internetlostMessage">
                        <x-svg-icon icon="internet-disconnected" /> @lang('quickadmin.qa_disconnected')
                    </div>
                    <div class="bg-success alertMessage d-none my-1 text-center text-light py-2 px-3" id="OnlineComeBack">
                        <x-svg-icon icon="internet-connected" /> @lang('quickadmin.qa_connected')
                    </div>
                </div>
            </li>

          <li class="dropdown"><a href="#" data-toggle="dropdown"
              class="nav-link dropdown-toggle nav-link-lg nav-link-user">
              <img alt="image" src="{{ asset('admintheme/assets/img/user.png') }}" class="user-img-radious-style">
              <span class="d-sm-none d-lg-inline-block"></span></a>
            <div class="dropdown-menu dropdown-menu-right">
              <div class="dropdown-title">Hello {{ Auth::user()->name }}</div>
              <a href="{{route('user.profile')}}" class="dropdown-item has-icon">
                <i class="far fa-user"></i> Profile
              </a>
              <a href="{{route('user.change-password')}}" class="dropdown-item has-icon">
                <i class="fas fa-lock"></i> @lang('quickadmin.qa_change_password')
              </a>
              <div class="dropdown-divider"></div>
              <a href="{{ route('logout') }}" class="dropdown-item has-icon text-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
              </a>
            </div>
          </li>
        </ul>
      </nav>
