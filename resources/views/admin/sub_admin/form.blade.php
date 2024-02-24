<div class="form-label">
    <label>@lang('cruds.client_admin.fields.name'):</label>
    <input type="text" name="name" value="{{ (isset($subAdmin) && !empty($subAdmin->name)) ? $subAdmin->name : ''}}" required>
</div>
<div class="form-label">
    <label> @lang('cruds.client_admin.fields.email'):</label>
    <input type="email" name="email" {{ isset($subAdmin) ? 'disabled' : '' }} value="{{ (isset($subAdmin) && !empty($subAdmin->email)) ? $subAdmin->email : ''}}" required>
</div>
@if(!isset($subAdmin))
    <div class="form-label password-area">
        <label>@lang('cruds.client_admin.fields.password'):</label>
        <input type="password" name="password" value="" required>

        <span class="toggle-password close-eye">
            <x-svg-icons icon="close-eye" />
            <x-svg-icons icon="open-eye" />
        </span>
    </div>
@endif


<div class="form-label justify-content-center">
    <button type="submit" class="cbtn submitBtn">
        @if(isset($subAdmin))
            @lang('global.update') @lang('cruds.client_admin.title_singular')            
        @else
            @lang('global.add') @lang('cruds.client_admin.title_singular')
        @endif
    </button>
</div>