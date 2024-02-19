<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta name="author" content="shiftSpan">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ getSetting('site_title') ? getSetting('site_title') : config('app.name') }} | @yield('title')</title>
    <!-- Favicon -->
    <link rel="icon" href="{{ getSetting('site_logo') ? getSetting('site_logo') : asset(config('constant.default.favicon')) }}">
    <!-- Poppins Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.min.css') }}">
    <!-- Main css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/main.css') }}">
</head>
<body>
    <div class="loader"></div>
   
    @yield('main-content')
  
    <!-- Jquery Library -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <!-- Bootstrap Js -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

    @yield('customJS')

  </body>
</html>
