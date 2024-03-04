@extends('layouts.app')
@section('title', trans('global.dashboard'))
@section('customCss')
@endsection

@section('main-content')

<div class="dashbaord-content white-bg radius-50 space-30 animate__animated animate__fadeInUp">
    <h1 class="text-center mb-3">Welcome to Klive's Kitchen </h1>
    <div class="count-area d-flex justify-content-center">
        <a href="{{route('staffs.index')}}" class="count-box">
            <p>@lang('cruds.dashboard.fields.register_staff')</p>
            <span>25</span>
        </a>
        <a href="{{route('shifts.index')}}" class="count-box">
            <p>@lang('cruds.dashboard.fields.total_shifts')</p>
            <span>200</span>
        </a>
        <a href="{{route('locations.index')}}" class="count-box">
            <p>@lang('cruds.dashboard.fields.business_location')</p>
            <span>5</span>
        </a>
    </div>
</div>

@endsection
@section('customJS')
@endsection
