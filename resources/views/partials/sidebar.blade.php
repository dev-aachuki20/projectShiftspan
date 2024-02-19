<div class="sidebar-menu">
    <aside>
        <div class="d-lg-none text-end sidebar-close">
            <span>
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="26" height="26" x="0" y="0" viewBox="0 0 26 26" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><g data-name="02 User"><path d="M1.27 26a1.27 1.27 0 0 1 -0.898 -2.167l23.461 -23.461a1.27 1.27 0 0 1 1.796 1.796l-23.461 23.461A1.266 1.266 0 0 1 1.27 26z" fill="#ffffff" opacity="1" data-original="#ffffff" class=""/><path d="M24.73 26a1.266 1.266 0 0 1 -0.898 -0.372l-23.461 -23.461A1.27 1.27 0 0 1 2.167 0.372l23.461 23.461A1.27 1.27 0 0 1 24.73 26z" fill="#ffffff" opacity="1" data-original="#ffffff" class=""/></g></g></svg>
            </span>
        </div>
        <ul>
            <li>
                <a href="{{ route('dashboard')}}" class="{{ request()->is('admin/dashboard*') ? 'active' : '' }}" title="Dashboard">@lang('quickadmin.dashboard.title')</a>
            </li>

            <li>
                <a href="javascript:void(0);" title="Messages">Messages</a>
            </li>

            <li>
                <a href="javascript:void(0);" title="Client Admin">Client Admin</a>
            </li>

            <li>
                <a href="javascript:void(0);" title="Client Details">Client Details</a>
            </li>

            <li>
                <a href="{{ route('shifts.index')}}" class="{{ request()->is('admin/shifts') || request()->is('admin/shifts/*') ? 'active' : '' }}" title="@lang('quickadmin.shift.title')">@lang('quickadmin.shift.title')</a>
            </li>

            <li>
                <a href="javascript:void(0);" title="Staff">Staff</a>
            </li>

            @can('location_access')
                <li>
                    <a href="{{ route('locations.index')}}" class="{{ request()->is('admin/locations') || request()->is('admin/locations/*') ? 'active' : '' }}" title="@lang('cruds.location.title')">@lang('cruds.location.title')</a>
                </li>
            @endcan
            @can('occupation_access')
                <li>
                    <a href="{{ route('occupations.index')}}" class="{{ request()->is('admin/occupations') || request()->is('admin/occupations/*') ? 'active' : '' }}" title="@lang('cruds.occupation.title')">@lang('cruds.occupation.title')</a>
                </li>
            @endcan
            @if(auth()->user()->is_super_admin)
                <li>
                    <a href="{{route('show.setting')}}" title="@lang('cruds.setting.title')">@lang('cruds.setting.title')</a>
                </li>
            @endif
        </ul>
    </aside>
</div>
