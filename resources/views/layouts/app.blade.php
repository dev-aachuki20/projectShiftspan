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

		<div class="content-area  {{ request()->is('dashboard') ? 'dashboard-page' : '' }}">
			@include('partials.sidebar')
			@yield('main-content')
		</div>
	</div>
	<div class="popup_render_div"></div>
    @include('partials.fscript')
</body>


@yield('customJS')
</html>
