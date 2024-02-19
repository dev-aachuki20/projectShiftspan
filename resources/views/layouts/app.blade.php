<!DOCTYPE html>
<html lang="en">
<head>
	<title>{{ config('app.name') }} | @yield('title')</title>
    @include('partials.hscript')
    @yield('customCss')
</head>
<body>

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
</body>


@yield('customJS')
</html>
