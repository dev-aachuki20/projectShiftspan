<div class="form-label">
    <label>@lang('cruds.location.title_singular') @lang('cruds.location.fields.name'):</label>
    <input type="text" name="name" value="{{ (isset($location) && !empty($location->name)) ? $location->name : ''}}">
</div>
<div class="form-label justify-content-center">
    <button type="submit" class="cbtn submitBtn">
        @if(isset($location))
            @lang('global.update') @lang('cruds.location.title_singular')            
        @else
            @lang('global.add') @lang('cruds.location.title_singular')
        @endif
    </button>
</div>