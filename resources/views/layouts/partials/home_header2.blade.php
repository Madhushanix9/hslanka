<header id="topnav" class="defaultscroll sticky">
    <div class="container">
        <!-- Logo container-->
        <a class="logo" href="index.html">
            <img src="https://shreethemes.in/landrick/landing/assets/images/logo-dark.png" height="24"
                class="logo-light-mode" alt="">
            <img src="https://shreethemes.in/landrick/landing/assets/images/logo-dark.png" height="24"
                class="logo-dark-mode" alt="">
        </a>
        <!-- Logo End -->

        <div class="menu-extras">
            <div class="menu-item">
                <!-- Mobile menu toggle-->
                <a class="navbar-toggle" id="isToggle" onclick="toggleMenu()">
                    <div class="lines">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </a>
                <!-- End mobile menu toggle-->
            </div>
        </div>

        <!--Login button Start-->
        {{-- <ul class="buy-button list-inline mb-0">
          <li class="list-inline-item mb-0">
              <a href="javascript:void(0)" class="btn btn-icon btn-light">
                  <img src="assets/images/app/app-store.png" class="avatar avatar-ex-small p-1" alt="">
              </a>
          </li>
  
          <li class="list-inline-item mb-0">
              <a href="javascript:void(0)" class="btn btn-icon btn-light">
                  <img src="assets/images/app/play-store.png" class="avatar avatar-ex-small p-1" alt="">
              </a>
          </li>
      </ul> --}}
        <ul class="buy-button list-inline mb-0">
            <button type="button" class="btn btn-danger">Demo</button>
        </ul>
        {{-- <button type="button" class="btn btn-danger">Danger</button> --}}
        <!--Login button End-->

        <div id="navigation">
            <!-- Navigation Menu-->
            <ul class="navigation-menu">
                <li><a href="{{ action('HomeController@index') }}" class="sub-menu-item">Home</a></li>
                <li><a href="#!" class="sub-menu-item">Prices</a></li>
                <li><a href="#!" class="sub-menu-item">Support</a></li>
                @if (Route::has('login'))
                    @if (!Auth::check())
                        <li class="has-submenu parent-menu-item">

                            <a href="javascript:void(0)">Login</a><span class="menu-arrow"></span>
                            <ul class="submenu">
                                <li><a href="{{ route('login') }}" class="sub-menu-item">Login</a></li>
                                @if (config('constants.allow_registration'))
                                    <li><a href="{{ route('business.getRegister') }}" class="sub-menu-item">Register</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                @endif
            </ul>
        </div>
    </div>
</header>
