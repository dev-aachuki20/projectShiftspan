@if(auth()->user()->is_super_admin)
    <div class="form-label select-label">
        <label>@lang('cruds.shift.fields.client_name'):</label>
        <select name="sub_admin_id" id="sub_admin_id" class="select2" @required(true)>
            <option value="">@lang('global.select') @lang('cruds.shift.fields.client_name')</option>
            @foreach ($subAdmins as $key => $value)
                <option value="{{$key}}" @selected(isset($shift) && $shift->client->uuid == $key ) >{{ $value}}</option>
            @endforeach
        </select>
        <div class="client_name_error error_select" style="width: 100%"></div>
    </div>
@endif

<div class="form-label select-label">
    <label>@lang('cruds.shift.fields.client_detail_name'):</label>
    <select name="client_detail_id" id="client_detail_id" class="select2" @required(true)>
        <option value="">@lang('global.select') @lang('cruds.shift.fields.client_detail_name')</option>
        @if((isset($shift) && auth()->user()->is_super_admin) || auth()->user()->is_sub_admin)        
            @foreach($clientDetails as $clientDetailKey => $clientDetail)
                <option value="{{$clientDetailKey}}" @selected(isset($shift) && $shift->clientDetail->uuid == $clientDetailKey ) >{{ $clientDetail}}</option>
            @endforeach
        @endif
    </select>
    <div class="client_detail_name_error error_select" style="width: 100%"></div>
</div>

<div class="form-label">
    <label>@lang('cruds.shift.fields.start_date'):</label>
    <input type="text" name="start_date" id="start_date" class="datepicker" value="{{ isset($shift) ? dateFormat($shift->start_date, config('constant.date_format.date')) : '' }}" @required(true) @readonly(true)/>
</div>
<div class="form-label">
    <label>@lang('cruds.shift.fields.end_date'):</label>
    <input type="text" name="end_date" id="end_date"  class="datepicker" value="{{ isset($shift) ? dateFormat($shift->end_date, config('constant.date_format.date')) : '' }}" @required(true) @readonly(true) />
</div>
<div class="form-label">
    <label>@lang('cruds.shift.fields.start_time'):</label>
    <input type="text" name="start_time" id="start_time" class="timepicker" value="{{ isset($shift) ? dateFormat($shift->start_time, config('constant.date_format.time')) : '' }}" @required(true) @readonly(false) />
</div>
<div class="form-label">
    <label>@lang('cruds.shift.fields.end_time'):</label>
    <input type="text" name="end_time" id="end_time" class="timepicker" value="{{ isset($shift) ? dateFormat($shift->end_time, config('constant.date_format.time')) : '' }}" @required(true) @readonly(false) />
</div>

<div class="form-label select-label">
    <label>@lang('cruds.shift.fields.occupation_id'):</label>
    <select name="occupation_id" id="occupation_id" class="select2" @required(true)>
        <option value="">@lang('global.select') @lang('cruds.shift.fields.occupation_id')</option>
        @if((isset($shift) && auth()->user()->is_super_admin) || auth()->user()->is_sub_admin)
            @foreach ($occupations as $occupationKey => $occupation)
                <option value="{{$occupationKey}}" @selected(isset($shift) && $shift->occupation->uuid == $occupationKey )>{{ $occupation  }}</option>
            @endforeach
        @endif
    </select>
    <div class="client_detail_name_error error_select" style="width: 100%"></div>
</div>

<div class="form-label select-label">
    <label>@lang('cruds.shift.fields.location_id'):</label>
    <select name="location_id" id="location_id" class="select2" @required(true)>
        <option value="">@lang('global.select') @lang('cruds.shift.fields.location_id')</option>
        @if((isset($shift) && auth()->user()->is_super_admin) || auth()->user()->is_sub_admin)
            @foreach ($locations as $locationKey => $location)
                <option value="{{$locationKey}}" @selected(isset($shift) && $shift->location->uuid == $locationKey ) >{{ $location}}</option>
            @endforeach
        @endif
    </select>
    <div class="client_detail_name_error error_select" style="width: 100%"></div>
</div>

@if(!isset($shift))
    <div class="form-label select-label">
        <label>@lang('cruds.shift.fields.quantity'):</label>
        <select name="quantity" id="quantity" class="select2" @required(true)>
            @for ($i=1; $i<=10;$i++)
                <option value="{{$i}}">{{ $i}}</option>
            @endfor
        </select>
        <div class="quantity_error error_select" style="width: 100%"></div>
    </div>
@endif
<div class="form-label select-label">
    <label>@lang('cruds.shift.fields.assign_staff'):</label>
    <select name="assign_staff" id="assign_staff" class="select2" >
        <option value="">@lang('global.select') @lang('cruds.staff.title_singular')</option>
        @if((isset($shift) && auth()->user()->is_super_admin) || auth()->user()->is_sub_admin)
            @foreach ($staffs as $keyStaff => $staff)
                <option value="{{$keyStaff}}" @selected(isset($shift) && in_array($keyStaff, $selectedStaffs) )>{{ $staff}}</option>
            @endforeach
        @endif
    </select>
    <div class="staff_error error_select" style="width: 100%"></div>
</div>

<div class="form-label justify-content-center">
    <button type="submit" class="cbtn submitBtn">
        @if(isset($shift))
            @lang('global.update') @lang('cruds.shift.title_singular')            
        @else
            @lang('global.add') @lang('cruds.shift.title_singular')
        @endif
    </button>
</div>