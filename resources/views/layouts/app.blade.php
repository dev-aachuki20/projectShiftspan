<!DOCTYPE html>
<html lang="en">
<head>
	<title>{{ getSetting('site_title') ? getSetting('site_title') : config('app.name') }} | @yield('title')</title>
    @include('partials.hscript')
    @yield('customCss')

	<!-- Main css -->
	<link rel="stylesheet" type="text/css" href="{{ asset('css/main.css') }}">
</head>
<body>
	<div class="loader-div" style="display: none"><div><img src="http://localhost:8000/default/datatable_loader.gif" width="100"></div></div>
	<div class="main-dashboard ">
        @include('partials.header')

		<div class="content-area  {{ request()->is('admin/dashboard*') ? 'dashboard-page' : '' }}">
			@include('partials.sidebar')
			<div class="right-content">
				@yield('main-content')
			</div>
		</div>
	</div>
	<div class="popup_render_div"></div>
	
    @include('partials.fscript')

	@yield('customJS')
</body>
</html>
