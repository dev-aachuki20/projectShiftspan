@extends('layouts.app')
@section('title', trans('cruds.message.title_singular'))

@section('customCss')
<link href="{{asset('plugins/jquery-ui/jquery.ui.min.css')}}" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('main-content')
    <div class="animate__animated animate__fadeInUp">
        <div class="msg-content white-bg radius-50 space-30 d-flex align-items-center">
            <h2 class="mb-md-0">@lang('cruds.message.fields.message_center')</h2>
            @can('message_create')
                <a href="javascript:void(0)" class="add_btn dash-btn green-bg" data-bs-toggle="modal" data-bs-target="#NnotificationSettings">+ @lang('cruds.message.fields.new_message') </a><div class="d-sm-none w-100"></div>
            @endcan
        </div>
    </div>

        {{-- Start Chat System --}}
        <div class="container-fluid px-0 pt-4 chatboxGroup" id="chatboxGroup">
            <div class="row h-100 gx-3">
                <div class="col-xxl-3 col-md-4 h-100 animate__animated animate__fadeInUp">
                    <div class="sidebar h-100 p-3 rounded-4">
                        <div class="user-list h-100">
                            <div class="row h-100 overflow-hidden flex-column flex-nowrap">
                                <div class="col-12 mb-3">
                                    <div class="input-group searchbar">
                                        <input type="text" id="groupSearch" class="form-control rounded-pill" placeholder="Group" aria-label="Group" aria-describedby="basic-addon1">
                                        <button class="shadow-none btn add_btn dash-btn green-bg w-115 m-0 border-0 rounded-pill">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M20.71 19.29L17.31 15.9C18.407 14.5025 19.0022 12.7767 19 11C19 9.41775 18.5308 7.87103 17.6518 6.55544C16.7727 5.23985 15.5233 4.21447 14.0615 3.60897C12.5997 3.00347 10.9911 2.84504 9.43928 3.15372C7.88743 3.4624 6.46197 4.22433 5.34315 5.34315C4.22433 6.46197 3.4624 7.88743 3.15372 9.43928C2.84504 10.9911 3.00347 12.5997 3.60897 14.0615C4.21447 15.5233 5.23985 16.7727 6.55544 17.6518C7.87103 18.5308 9.41775 19 11 19C12.7767 19.0022 14.5025 18.407 15.9 17.31L19.29 20.71C19.383 20.8037 19.4936 20.8781 19.6154 20.9289C19.7373 20.9797 19.868 21.0058 20 21.0058C20.132 21.0058 20.2627 20.9797 20.3846 20.9289C20.5064 20.8781 20.617 20.8037 20.71 20.71C20.8037 20.617 20.8781 20.5064 20.9289 20.3846C20.9797 20.2627 21.0058 20.132 21.0058 20C21.0058 19.868 20.9797 19.7373 20.9289 19.6154C20.8781 19.4936 20.8037 19.383 20.71 19.29ZM5 11C5 9.81332 5.3519 8.65328 6.01119 7.66658C6.67047 6.67989 7.60755 5.91085 8.7039 5.45673C9.80026 5.0026 11.0067 4.88378 12.1705 5.11529C13.3344 5.3468 14.4035 5.91825 15.2426 6.75736C16.0818 7.59648 16.6532 8.66558 16.8847 9.82946C17.1162 10.9933 16.9974 12.1997 16.5433 13.2961C16.0892 14.3925 15.3201 15.3295 14.3334 15.9888C13.3467 16.6481 12.1867 17 11 17C9.4087 17 7.88258 16.3679 6.75736 15.2426C5.63214 14.1174 5 12.5913 5 11Z" fill="white"/>
                                            </svg>                                            
                                        </button>
                                    </div>
                                </div>
                                {{-- Start Group List --}}
                                <div class="col-12 h-100 flex-fill overflow-y-auto group-list">
                                  
                                 @include('admin.message.partials.group')
                                   
                                </div>
                                {{-- End Group List --}}

                            </div>
                        </div>
                        <div class="sidebar-loader" style="display: none;">
                            <img src="../images/loader-spinner2.gif" alt="Loader">
                        </div>
                    </div>
                </div>
                <div class="col-xxl-9 col-md-8 chat-panel h-100 overflow-hidden">
                    <div class="card chatcard h-100 overflow-hidden animate__animated animate__fadeInUp">
                        <div class="screen-loader" style="display: none;">
                            <div class="s-loaderinner">
                                <img src="../images/loader-spinner.gif" alt="loader-image">
                            </div>
                        </div>
                        {{-- Start Group Chat Box --}}
                        <div class="row h-100 flex-column flex-nowrap overflow-hidden groupChatScreen">
                            @include('admin.message.partials.chatbox')
                        </div>
                        {{-- End Group Chat Box --}}

                    </div>
                </div>
            </div>
        </div>
        {{-- End Chat System --}}

    {{-- Notification model --}}
    @include('admin.staff.notification.create')
@endsection

@section('customJS')

@parent
<script src="{{asset('plugins/jquery-ui/jquery.ui.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

@include('admin.message.partials.custom-chat-js')

@endsection
