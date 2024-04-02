<header class="header">
    <div class="header-inner d-flex align-items-center justify-content-between animate__animated animate__fadeIn">
        <div class="menu-left">
            <div class="mobile-humberger d-inline-block d-lg-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="22" viewBox="0 0 30 22" fill="none">
                    <path d="M28.75 12.0834H1.25C0.56 12.0834 0 11.5234 0 10.8334C0 10.1434 0.56 9.58344 1.25 9.58344H28.75C29.44 9.58344 30 10.1434 30 10.8334C30 11.5234 29.44 12.0834 28.75 12.0834ZM28.75 2.5H1.25C0.56 2.5 0 1.94 0 1.25C0 0.56 0.56 0 1.25 0H28.75C29.44 0 30 0.56 30 1.25C30 1.94 29.44 2.5 28.75 2.5ZM28.75 21.6666H1.25C0.56 21.6666 0 21.1066 0 20.4166C0 19.7266 0.56 19.1666 1.25 19.1666H28.75C29.44 19.1666 30 19.7266 30 20.4166C30 21.1066 29.44 21.6666 28.75 21.6666Z" fill="#000000"/>
                </svg>
            </div>
            <ul>
                @if(auth()->user()->is_sub_admin)
                    <li class="d-none d-lg-inline-block"><a title="Company ID">@lang('cruds.header.fields.company_id'): <span>{{auth()->user()->company_number}}</span></a></li>
                @endif
                <li class="dropdown">
                    <a href="javascript:void(0)" title="Notifications/Alerts" class="has_noti dropdown-toggle notificationsBtn" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                        <span class="d-none d-lg-block">@lang('cruds.header.fields.notifications')</span><span class="d-lg-none">
                            <x-svg-icons icon="notification" />
                        </span></a>
                    <div class="dropdown-menu">
                        <ul class="notifications_area">Notification Landing</ul>
                        {{-- <ul class="dropdown-menu notifications_area">
                            <li>
                                <a href="javascript:void(0)" class="un-read">
                                    <h6>Shifts Picked</h6>
                                    <p>JASON DONNELLY picked 5 shifts. please refer to the shift list page for more information.</p>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" class="un-read">
                                    <h6>Message</h6>
                                    <p>JASON DONNELLY responded to payroll message. please refer to the message page for more info.</p>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)">
                                    <h6>Shifts Picked</h6>
                                    <p>VICKY MAYNARD picked a shift. please refer to the shift list page for more information. </p>
                                </a>
                            </li>
                        </ul> --}}
                    </div>
                </li>
            </ul>
        </div>
        <div class="logo-area px-2 px-xl-4">
            <a href="{{ route('dashboard') }}"><img src="{{ getSetting('site_logo') ? getSetting('site_logo') : asset(config('constant.default.logo')) }}" alt="{{ getSetting('site_title') ? getSetting('site_title') : config('app.name') }} | logo" class=""></a>
        </div>
        <div class="menu-right">
            <ul>
                <li class="dropdown">
                    <a href="javascript:void(0)" title="{{auth()->user()->name}}" class="active dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="d-none d-lg-inline-block" id="header_auth_name">{{auth()->user()->name}}</span>
                        <span class="menu-icon"><img id="header_profile_image" src="{{ auth()->user()->profile_image_url ? auth()->user()->profile_image_url : asset(config('constant.default.user_icon')) }}" alt="{{auth()->user()->name}}" class="img-fluid {{ auth()->user()->profile_image_url ? '' : 'default-image' }}"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="{{route('show.profile')}}" title="@lang('cruds.user.admin_profile.title')">@lang('cruds.user.admin_profile.title')</a></li>
                        <li><a href="{{route('show.change.password')}}" title="@lang('global.change_password')">@lang('global.change_password')</a></li>
                        <li class="d-lg-none"><a title="Company ID">@lang('cruds.header.fields.company_id'):<span>SS12457</span></a></li>
                        <li class="d-lg-none"><a href="javascript:void(0)" title="Help">@lang('cruds.header.fields.help')</a></li>
                        {{-- @if(auth()->user()->is_super_admin) --}}
                        <li class="d-lg-none"><a href="{{route('show.contact-detail')}}" title="@lang('cruds.setting.contact_details.title')">@lang('cruds.setting.contact_details.title')</a></li>
                        {{-- @endif --}}
                        <li class="d-lg-none"><a href="javascript:void(0)" title="Log Out">@lang('global.logout')</a></li>
                    </ul>
                </li>
                
                <li class="d-none d-lg-inline-block"><a href="javascript:void(0)" title="@lang('global.help')" data-bs-toggle="modal" data-bs-target="#HelpPdf">@lang('global.help')</a></li>
                
                {{-- @can('setting_access') --}}
                    <li class="d-none d-lg-inline-block">
                        <a href="{{route('show.contact-detail')}}" title="@lang('cruds.setting.contact_details.title')">@lang('cruds.setting.contact_details.title')</a>
                    </li>
                {{-- @endcan --}}
                <li class="d-none d-lg-inline-block"><a href="{{ route('logout')}}" title="Log Out">@lang('global.logout')</a></li>
            </ul>
        </div>
    </div>
</header>

<!-- Help Modal -->
<div class="modal fade common-modal modal-size-l" id="HelpPdf" tabindex="-1" aria-labelledby="HelpPdfLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-820">
        <div class="modal-content">
            <div class="modal-header justify-content-center green-bg">
                <h5 class="modal-title text-center" id="HelpPdfLabel">@lang('global.help')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <embed src="{{ getSetting('help_pdf') ? getSetting('help_pdf') : asset(config('constant.default.help_pdf')) }}" width="100%" height="500px"/>
            </div>
        </div>
    </div>
</div>