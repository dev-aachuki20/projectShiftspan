
@if(auth()->user()->is_super_admin)
    <div class="form-label">
        <label>@lang('cruds.occupation.title_singular') @lang('cruds.occupation.fields.name'):</label>
        <input type="text" name="name" value="{{ (isset($occupation) && !empty($occupation->name)) ? $occupation->name : ''}}">
    </div>

    <div class="form-label select-label">
        <label>@lang('cruds.occupation.fields.sub_admin'):</label>
        <select name="sub_admin[]" id="sub_admin" class="select2" multiple>
            @foreach ($subAdmins as $key => $subAdmin)
                <option value="{{$key}}" @selected(isset($selectedSubAdmins) && in_array($key, $selectedSubAdmins))>{{ $subAdmin }}</option>
            @endforeach
        </select>
        <div class="sub_admin_error" style="width: 100%"></div>
    </div>
@else
    <div class="form-label select-label">
        <label>@lang('cruds.occupation.title_singular') @lang('cruds.occupation.fields.name'):</label>
        <select name="occupation_name" id="occupation_name" class="select2" required>
            <option value="">@lang('global.select') @lang('cruds.occupation.title_singular')</option>
            <option value="new"> + @lang('global.add') @lang('cruds.occupation.title_singular')</option>
            @foreach ($occupations as $key => $value)
                <option value="{{$key}}">{{ $value}}</option>
            @endforeach
        </select>
        <div class="occupation_name_error" style="width: 100%"></div>
    </div>
@endif
<div class="form-label justify-content-center">
    <button type="submit" class="cbtn submitBtn">
        @if(isset($occupation))
            @lang('global.update') @lang('cruds.occupation.title_singular')            
        @else
            @lang('global.add') @lang('cruds.occupation.title_singular')
        @endif
    </button>
</div>