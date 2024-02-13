@extends('layouts.app')
@section('title')@lang('quickadmin.dashboard.title')@endsection
@section('customCss')
<meta name="csrf-token" content="{{ csrf_token() }}" >
@endsection

@section('main-content')

    <div class="right-content">
        <div class="dashbaord-content white-bg radius-50 space-30 animate__animated animate__fadeInUp">
            <h1 class="text-center mb-3">Welcome to Klive's Kitchen </h1>
            <div class="count-area d-flex justify-content-center">
                <a href="staff.html" class="count-box">
                    <p>Registered Staff</p>
                    <span>25</span>
                </a>
                <a href="shifts.html" class="count-box">
                    <p>Total Shifts</p>
                    <span>200</span>
                </a>
                <a href="location.html" class="count-box">
                    <p>Business Locations</p>
                    <span>5</span>
                </a>
            </div>
        </div>
    </div>

@endsection
@section('customJS')
@endsection
