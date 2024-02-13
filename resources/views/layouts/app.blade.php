<!DOCTYPE html>
<html lang="en">
<head>
	<title>@yield('title')</title>
    @include('partials.hscript')
    @yield('customCss')
</head>
<body>

	<div class="main-dashboard ">
        @include('partials.header')

		<div class="content-area">
			@include('partials.sidebar')
			@yield('main-content')
		</div>
	</div>
    @include('partials.fscript')
</body>


@yield('customJS')
</html>
