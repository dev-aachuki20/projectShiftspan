<div class="form-label">
    <label>@lang('cruds.occupation.title_singular') @lang('cruds.occupation.fields.name'):</label>
    <input type="text" name="name" value="{{ (isset($occupation) && !empty($occupation->name)) ? $occupation->name : ''}}">
</div>
<div class="form-label justify-content-center">
    <button type="submit" class="cbtn submitBtn">
        @if(isset($occupation))
            @lang('global.update') @lang('cruds.occupation.title_singular')            
        @else
            @lang('global.add') @lang('cruds.occupation.title_singular')
        @endif
    </button>
</div>