@extends('layouts.auth')
@section('title','Login')
@section('main-content')

  <div class="splash-screen position-relative d-flex align-items-center justify-content-center">
    <div class="spalsh-carea text-center">
      <a href="{{ route('login') }}"><img src="{{ asset(config('app.default.logo')) }}" alt="Shift Span | logo" class="img-fluid"></a>
      <div class="login-form">
        <form method="POST" action="{{route("authenticate")}}">
          @csrf

          <div class="form-label position-relative">
            <input type="text" name="username" placeholder="Username/Email">
            <span class="input-icon"><img src="{{ asset('images/user-icon.svg') }}" alt="user icon"></span>
          </div>
          @error('username')
            <span class="invalid-feedback d-block" role="alert">
                {{ $message }}
            </span>
          @enderror

          <div class="form-label position-relative">
            <input type="password" name="password" placeholder="Password" id="password" autocomplete="off">
            <span class="input-icon"><img src="{{ asset('images/padlock-icon.svg') }}" alt="padlock icon"></span>
          </div>

          @error('password')
            <span class="invalid-feedback d-block" role="alert">
                {{ $message }}
            </span>
          @enderror

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
 
@endsection
