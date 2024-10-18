<div class="row first_row">
    <div class="col-lg-4">
        <div class="form-label position-relative">
            <label>@lang('cruds.shift.fields.shift_label'):</label>
            <input type="text" name="shift_label" id="shift_label"  value="{{ isset($shift) ? $shift->shift_label : '' }}" autocomplete="off" @required(true) />
        </div>
    </div>
    <div class="col-lg-4">
        @if(auth()->user()->is_super_admin)
            <div class="form-label select-label">
                <label>@lang('cruds.shift.fields.client_name'):</label>
                <select name="sub_admin_id" id="sub_admin_id" class="select2">
                    <option value="">@lang('global.select') @lang('cruds.shift.fields.client_name')</option>
                    @foreach ($subAdmins as $key => $value)
                        <option value="{{$key}}" @selected(isset($shift) && $shift->client->uuid == $key ) >{{ $value}}</option>
                    @endforeach
                </select>
                <div class="client_name_error error_select" style="width: 100%"></div>
            </div>
        @endif
    </div>
    <div class="col-lg-4">
        <div class="form-label select-label">
            <label>@lang('cruds.shift.fields.client_detail_name'):</label>
            <select name="client_detail_id" id="client_detail_id" class="select2">
                <option value="">@lang('global.select') @lang('cruds.shift.fields.client_detail_name')</option>
                @if((isset($shift) && auth()->user()->is_super_admin) || auth()->user()->is_sub_admin)        
                    @foreach($clientDetails as $clientDetailKey => $clientDetail)
                        <option value="{{$clientDetailKey}}" @selected(isset($shift) && $shift->clientDetail->uuid == $clientDetailKey ) >{{ $clientDetail}}</option>
                    @endforeach
                @endif
            </select>
            <div class="client_detail_name_error error_select" style="width: 100%"></div>
        </div>
    </div>
</div>

<div class="row clone_row">
    <div class="inner_col">
        <div class="form-label position-relative">
            <label>@lang('cruds.shift.fields.start_date'):</label>
            <input type="text" name="start_date" id="start_date" class="datepicker" value="{{ isset($shift) ? dateFormat($shift->start_date, config('constant.date_format.date')) : '' }}" @required(true) @readonly(true)/>
        </div>
    </div>
    <div class="inner_col">
        <div class="form-label">
            <label>@lang('cruds.shift.fields.end_date'):</label>
            <input type="text" name="end_date" id="end_date"  class="datepicker" value="{{ isset($shift) ? dateFormat($shift->end_date, config('constant.date_format.date')) : '' }}" @required(true) @readonly(true) />
        </div>
    </div>
    <div class="inner_col">
        <div class="form-label">
            <label>@lang('cruds.shift.fields.start_time'):</label>
            <input type="text" name="start_time" id="start_time" class="timepicker" value="{{ isset($shift) ? dateFormat($shift->start_time, config('constant.date_format.time')) : '' }}" @required(true) @readonly(false) oninput="this.value = this.value.replace(/[^0-9:]/g, '').replace(/(\..*)\./g, '$1');" onkeypress="return /[0-9:]/.test(event.key)" maxlength="5"/>
        </div>
    </div>
    <div class="inner_col">
        <div class="form-label">
            <label>@lang('cruds.shift.fields.end_time'):</label>
            <input type="text" name="end_time" id="end_time" class="timepicker" value="{{ isset($shift) ? dateFormat($shift->end_time, config('constant.date_format.time')) : '' }}" @required(true) @readonly(false) oninput="this.value = this.value.replace(/[^0-9:]/g, '').replace(/(\..*)\./g, '$1');" onkeypress="return /[0-9:]/.test(event.key)" maxlength="5"/>
        </div>
    </div>
    <div class="inner_col">
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
    </div>
    <div class="AddOptionBtn2">
        <a href="javascript:void(0)" class="addicon">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                <g clip-path="url(#clip0_966_53)">
                <path d="M17.0999 8.1H9.90005V0.899945C9.90005 0.403253 9.4968 0 8.99994 0C8.50325 0 8.1 0.403253 8.1 0.899945V8.1H0.899945C0.403253 8.1 0 8.50325 0 8.99994C0 9.4968 0.403253 9.90005 0.899945 9.90005H8.1V17.0999C8.1 17.5968 8.50325 18.0001 8.99994 18.0001C9.4968 18.0001 9.90005 17.5968 9.90005 17.0999V9.90005H17.0999C17.5968 9.90005 18.0001 9.4968 18.0001 8.99994C18.0001 8.50325 17.5968 8.1 17.0999 8.1Z" fill="white"/>
                </g>
                <defs>
                <clipPath id="clip0_966_53">
                <rect width="18" height="18" fill="white"/>
                </clipPath>
                </defs>
            </svg>
        </a>
    </div>
</div>
<div id="clone-showing-data"></div>

<div class="row mb-4">
    <div class="col-lg-6">
        <div class="form-label select-label">
            <label>@lang('cruds.shift.fields.occupation_id'):</label>
            <select name="occupation_id" id="occupation_id" class="select2">
                <option value="">@lang('global.select') @lang('cruds.shift.fields.occupation_id')</option>
                @if((isset($shift) && auth()->user()->is_super_admin) || auth()->user()->is_sub_admin)
                    @foreach ($occupations as $occupationKey => $occupation)
                        <option value="{{$occupationKey}}" @selected(isset($shift) && $shift->occupation->uuid == $occupationKey )>{{ $occupation  }}</option>
                    @endforeach
                @endif
            </select>
            <div class="client_detail_name_error error_select" style="width: 100%"></div>
        </div>
    </div>
    <div class="col-lg-6">
        @if(!isset($shift))
            <div class="form-label select-label">
                <label>@lang('cruds.shift.fields.quantity'):</label>
                <select name="quantity" id="quantity" class="select2">
                    @for ($i=1; $i<=10;$i++)
                        <option value="{{$i}}">{{ $i}}</option>
                    @endfor
                </select>
                <div class="quantity_error error_select" style="width: 100%"></div>
            </div>
        @endif
    </div>
</div>
<div class="form-label justify-content-center mb-0">
    <button type="submit" class="cbtn submitBtn">
        @if(isset($shift))
            @lang('global.update') @lang('cruds.shift.title_singular')            
        @else
            @lang('global.add') @lang('cruds.shift.title_singular')
        @endif
    </button>
</div>
<script>
    $(document).on('click', '.addicon', function() {
        let clonedDiv = $(".clone_row").first().clone();
        let count = $('.clone_row').length;
        clonedDiv.find('.AddOptionBtn2').html('<a href="javascript:void(0)" class="remove-option"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M1.07996 10.0798H16.9199C17.5165 10.0798 18 9.59626 18 8.99954C18 8.40296 17.5166 7.91943 16.9199 7.91943H1.07996C0.483527 7.91958 0 8.40311 0 8.99969C0 9.59626 0.483527 10.0798 1.07996 10.0798Z" fill="white"/></svg></a>');
        clonedDiv.appendTo("#clone-showing-data");
        cloneIndex++;
        
    });
    $(document).on('click', '.remove-option', function() {
        $(this).closest('.clone_row').remove();
    });
</script>