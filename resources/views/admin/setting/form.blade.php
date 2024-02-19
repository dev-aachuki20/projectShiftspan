<form class="msg-form" id="settingform" acction="{{route('update.setting')}}" enctype="multipart/form-data">
    @csrf
    @foreach($settings as $key => $setting)
        @if($setting->type == 'text')
            <div class="form-label">
                <label>@lang('cruds.setting.fields.site_title'):</label>
                <input type="text" value="{{$setting->value}}" name="{{$setting->key}}" />
            </div>
        @endif

        @if($setting->type == 'image')
            <div class="form-label">
                <label>@lang('cruds.setting.fields.site_logo'):</label>
                <div class="right-sidebox">
                    <div class="img-prevarea img-prePro">
                        <img src="images/dummy-image-square.jpg">
                    </div>
                </div>
            </div>
            <div class="form-label">
                <label>@lang('cruds.setting.fields.icon_image'):</label>
                <div class="right-sidebox">
                    <div class="img-prevarea img-prePro icon-size">
                        <img src="images/dummy-image-square.jpg">
                    </div>
                </div>
            </div>
            <div class="form-label">
                <label>@lang('cruds.setting.fields.change_logo'):</label>
                <div class="right-sidebox">
                    <div class="chose-btn-area position-relative">
                        <a href="javascript:void(0)" class="chose-btn mt-0">@lang('global.choose') @lang('global.image')</a>
                        <input type="file" name="{{$setting->key}}" id="image-input" class="fileInputBoth" accept="image/*">
                    </div>
                </div>
            </div>
        @endif
        
        @if($setting->type == 'file')                    
            <div class="form-label">
                <label>@lang('cruds.setting.fields.change_logo'):</label>
                <div class="right-sidebox">
                    <div class="chose-btn-area position-relative">
                        <a href="javascript:void(0)" class="chose-btn mt-0">@lang('global.choose') @lang('global.pdf')</a>
                        <input type="file" name="{{$setting->key}}" id="pdf-input" class="fileInputPdf" accept=".pdf">
                    </div>
                </div>
            </div>
        @endif
    @endforeach
    
    <div class="form-label justify-content-center">
        <input type="submit" value="@lang('global.update')" class="cbtn">
    </div>
</form>

