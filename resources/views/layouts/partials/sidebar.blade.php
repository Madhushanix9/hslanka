<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">

	<a href="{{route('home')}}" class="logo">
		<span class="logo-lg">{{ Session::get('business.name') }}</span>
	</a>

    <!-- Sidebar Menu -->
    {!! Menu::render('admin-sidebar-menu', 'adminltecustom'); !!}

    <!-- /.sidebar-menu -->
  </section>
  <!-- /.sidebar -->
</aside>

<style>

    .skin-blue-light .main-sidebar {
        background: transparent !important;
    }

    .sidebar-menu li a {
        color: #ffffff !important;
    }
    .skin-blue-light .sidebar-menu > li > ul li {
      background-color: #0499d1;
      border-radius: 2px;
      margin: 0 1px;
    }

    .skin-blue-light .sidebar-menu > li.menu-open > a,
    .skin-blue-light .sidebar-menu > li.active > a {
      background-color: #021a41 !important; 
        border-radius: 5px;
        color: white !important;
        font-weight: bold;
    }

    .skin-blue-light .sidebar-menu > li.menu-open > ul,
    .skin-blue-light .sidebar-menu > li.active > ul {
        background-color: transparent !important; 
    }

    .skin-blue-light .sidebar-menu > li > a:hover {
        background-color: #021a41 !important; 
        margin: 0 5px;
        border-radius: 5px;

    }

    .skin-blue-light .sidebar-menu > li > ul li a {
        color: #ffffff !important;
        transition: all 0.3s ease-in-out;
        padding-left: 10px;
    }

    .skin-blue-light .sidebar-menu > li > ul li a:hover {
        background-color: #021a41 !important;
        color: #ffffff !important;
        padding-left: 20px;/
        border-radius: 5px;
      margin: 0 2px;

    }

</style>

