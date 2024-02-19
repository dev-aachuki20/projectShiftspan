@extends('layouts.auth')
@section('title', trans('global.login'))
@section('main-content')

  <div class="splash-screen position-relative d-flex align-items-center justify-content-center">

    <div class="spalsh-carea text-center">
      <a href="{{ route('login') }}">
        <img src="{{ getSetting('site_logo') ? getSetting('site_logo') : asset(config('constant.default.logo')) }}" alt="{{ getSetting('site_title') ? getSetting('site_title') : config('app.name') }} | logo" class="img-fluid logo">
      </a>
      <div class="login-form">
        <form method="POST" action="{{route("authenticate")}}">
          @csrf
            @error('wrongcrendials')
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @enderror
          <div class="form-label position-relative">
            <input type="text" name="email" placeholder="@lang('quickadmin.qa_username')">
            <span class="input-icon"><img src="{{ asset('images/user-icon.svg') }}" alt="user icon"></span>
            @error('email')
            <span class="invalid-feedback d-block">
                {{ $message }}
            </span>
            @enderror
          </div>

          <div class="form-label position-relative password-area">
            <input type="password" name="password" placeholder="@lang('quickadmin.qa_password')" id="password" autocomplete="off">
            
            <span class="toggle-password close-eye">
              <x-svg-icons icon="close-eye" />
              <x-svg-icons icon="open-eye" />
            </span>
            <span class="input-icon"><img src="{{ asset('images/padlock-icon.svg') }}" alt="padlock icon"></span>
            @error('password')
            <span class="invalid-feedback d-block">
                {{ $message }}
            </span>
            @enderror
          </div>

          <div class="form-label text-center">
            <input type="submit" value="{{ strtoupper(__('global.sign_in'))}}" class="cbtn">
          </div>
        </form>
      </div>
    </div>
    <div class="shape-design">
      <img src="{{ asset('images/shape-design.svg') }}" alt="shape design" class="img-fluid">
    </div>
  </div>

@endsection

@section('customJS')
<script>

  // Password field hide/show functiolity
  $(document).on('click', '.toggle-password', function () {        
        var passwordInput = $(this).prev('input');        
        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            $(this).removeClass('close-eye').addClass('open-eye');
        } else {
            passwordInput.attr('type', 'password');
            $(this).removeClass('open-eye').addClass('close-eye');
        }
    });
</script>
@endsection
