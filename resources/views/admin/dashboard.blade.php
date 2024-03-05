@extends('layouts.app')
@section('title', trans('global.dashboard'))
@section('customCss')
@endsection

@section('main-content')

<div class="dashbaord-content white-bg radius-50 space-30 animate__animated animate__fadeInUp">
    @if(Auth::user()->roles->first()->name == 'Sub Admin')
        <h1 class="text-center mb-3">@lang('global.welcome_to') {{ ucfirst(Auth::user()->name) }} </h1>
    @endif

    <div class="count-area d-flex justify-content-center">
        <a href="{{route('staffs.index')}}" class="count-box">
            <p>@lang('cruds.dashboard.fields.register_staff')</p>
            <span>{{$data['userCount']}}</span>
        </a>
        <a href="{{route('shifts.index')}}" class="count-box">
            <p>@lang('cruds.dashboard.fields.total_shifts')</p>
            <span>{{$data['shiftCount']}}</span>
        </a>
        <a href="{{route('locations.index')}}" class="count-box">
            <p>@lang('cruds.dashboard.fields.business_location')</p>
            <span>{{$data['locationCount']}}</span>
        </a>
    </div>
</div>

@endsection
@section('customJS')
@endsection
