@if(auth()->user()->is_super_admin)
    <div class="form-label">
        <label>@lang('cruds.location.title_singular') @lang('cruds.location.fields.name'):</label>
        <input type="text" name="name" value="{{ (isset($location) && !empty($location->name)) ? $location->name : ''}}">
    </div>

    <div class="form-label select-label">
        <label>@lang('cruds.location.fields.sub_admin'):</label>
        <select name="sub_admin[]" id="sub_admin" class="select2" multiple>
            @foreach ($subAdmins as $key => $subAdmin)
                <option value="{{$key}}" {{ isset($selectedSubAdmins) && in_array($key, $selectedSubAdmins) ? 'selected' : '' }}>{{ $subAdmin }}</option>
            @endforeach
        </select>
        <div class="sub_admin_error" style="width: 100%"></div>
    </div>
@else
    <div class="form-label select-label">
        <label>@lang('cruds.location.title_singular') @lang('cruds.location.fields.name'):</label>
        <select name="location_name" id="location_name" class="select2" required>
            <option value="">@lang('global.select') @lang('cruds.location.title_singular')</option>
            <option value="new"> + @lang('global.add') @lang('cruds.location.title_singular')</option>
            @foreach ($locations as $key => $value)
                <option value="{{$key}}">{{ $value}}</option>
            @endforeach
        </select>
        <div class="location_name_error" style="width: 100%"></div>
    </div>
@endif
<div class="form-label justify-content-center">
    <button type="submit" class="cbtn submitBtn">
        @if(isset($location))
            @lang('global.update') @lang('cruds.location.title_singular')            
        @else
            @lang('global.add') @lang('cruds.location.title_singular')
        @endif
    </button>
</div>