<form class="msg-form" id="settingform" enctype="multipart/form-data">
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
                        <img src="{{ $setting->image_url ? $setting->image_url : asset(config('constant.default.logo')) }}">
                    </div>
                </div>
            </div>
            <div class="form-label">
                <label>@lang('cruds.setting.fields.icon_image'):</label>
                <div class="right-sidebox">
                    <div class="img-prevarea img-prePro icon-size">
                        <img src="{{ $setting->image_url ? $setting->image_url : asset(config('constant.default.logo')) }}">
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
                <label>{{$setting->display_name}}:</label>
                <div class="right-sidebox">
                    <div class="chose-btn-area position-relative">
                        <span class="d-inline-block position-relative">
                            <a href="javascript:void(0)" class="chose-btn mt-0">@lang('global.choose') @lang('global.pdf')</a>
                            <input type="file" name="{{$setting->key}}" id="pdf-input" class="fileInputPdf" accept=".pdf">
                        </span>
                        @if($setting->doc_url)
                            <a target="_blank" href="{{ $setting->doc_url }}" class="chose-btn mt-0">
                                <x-svg-icons icon="help-pdf" />
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    @endforeach
    
    <div class="form-label justify-content-center">
        <input type="submit" value="@lang('global.update')" class="cbtn submitBtn">
    </div>
</form>

