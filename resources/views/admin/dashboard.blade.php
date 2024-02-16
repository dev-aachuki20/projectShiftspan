@extends('layouts.app')
@section('title')@lang('quickadmin.dashboard.title')@endsection
@section('customCss')
@endsection

@section('main-content')

<div class="dashbaord-content white-bg radius-50 space-30 animate__animated animate__fadeInUp">
    <h1 class="text-center mb-3">Welcome to Klive's Kitchen </h1>
    <div class="count-area d-flex justify-content-center">
        <a href="javascript:void(0)" class="count-box">
            <p>Registered Staff</p>
            <span>25</span>
        </a>
        <a href="javascript:void(0)" class="count-box">
            <p>Total Shifts</p>
            <span>200</span>
        </a>
        <a href="{{route('locations.index')}}" class="count-box">
            <p>Business Locations</p>
            <span>5</span>
        </a>
    </div>
</div>

@endsection
@section('customJS')
@endsection
