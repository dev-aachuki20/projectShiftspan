<form action="{{ route('settings.update') }}" method="POST" id="settingform" enctype="multipart/form-data">
    <div class="row">
            <div class="col-lg-12">

                @foreach ($settings as $setting)
                <div class="form-group">
                    <label for="{{ $setting->key }}">{{ $setting->display_name }}</label>
                    @if ($setting->type === 'image')
                        <input type="file" class="form-control" name="{{ $setting->key }}" value="{{ isset($settings) ? $setting->value : old($setting->value) }}" id="{{ $setting->key }}" autocomplete="true">
                    @elseif ($setting->type === 'number')
                        <input type="number" class="form-control" name="{{ $setting->key }}" value="{{ isset($settings) ? $setting->value : old($setting->value) }}" id="{{ $setting->key}}" autocomplete="true">
                    @elseif ($setting->type === 'text')
                        <input type="text" class="form-control" name="{{ $setting->key }}" value="{{ isset($settings) ? $setting->value : old($setting->value) }}" id="{{ $setting->key}}" autocomplete="true">
                    @elseif($setting->type == 'text_area')
                        @if($setting->details)
                            @php
                            $parameterArray = explode(', ',$setting->details);
                            @endphp
                            @if($parameterArray)
                                @foreach($parameterArray as $parameter)
                                <button type="button" class="btn btn-sm btn-info copy-btn mb-1 p-1 font-weight-bold" data-elementVal="{{$parameter}}" data-targetTextareaId="{{ $setting->key }}">{{ $parameter }}</button>
                                @endforeach
                            @endif
                        @endif
                        <textarea class="summernote" id="{{ $setting->key}}" data-elementName ="{{$setting->key}}" placeholder="{{$setting->display_name}}" name="{{ $setting->key}}" rows="4">{{$setting->value}}</textarea>
                    @endif
                </div>
                @endforeach
            </div>

        <div class="col-md-6">
            <button type="submit" class="btn btn-primary">@lang('quickadmin.qa_submit')</button>
        </div>
    </div>
</form>

