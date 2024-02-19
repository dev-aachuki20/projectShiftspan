@extends('layouts.app')
@section('title')@lang('quickadmin.dashboard.title')@endsection
@section('customCss')

@endsection

@section('main-content')

<div class="animate__animated animate__fadeInUp">
    <div class="msg-content white-bg radius-50 space-30 d-flex align-items-center">
        <h2 class="mb-md-0">@lang('global.update') @lang('cruds.setting.title_singular')</h2>
    </div>
    <div class="profile-form mw-820 mx-auto pt-5 modal-size-l">
        <form class="msg-form" id="setting-form" enctype="multipart/form-data">
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
    </div>
</div>

@endsection
@section('customJS')
<script>
    $(document).on('submit', '#profile-form', function(e){
        e.preventDefault();
        $(".submitBtn").attr('disabled', true);

        $('.validation-error-block').remove();

        var formData = new FormData(this);

        $.ajax({
            type: 'post',
            url: "{{ route('update.profile') }}",
            dataType: 'json',
            contentType: false,
            processData: false,
            data: formData,
            success: function (response) {
                if(response.success) {
                    updateHeaderProfile(response.profile_image, response.auth_name);
                    toasterAlert('success',response.message);
                }
            },
            error: function (response) {
                console.log(response);
                if(response.responseJSON.error_type == 'something_error'){
                    toasterAlert('error',response.responseJSON.error);
                } else {                    
                    var errorLabelTitle = '';
                    $.each(response.responseJSON.errors, function (key, item) {
                        errorLabelTitle = '<span class="validation-error-block">'+item+'</sapn>';
                        
                        $(errorLabelTitle).insertAfter("input[name='"+key+"']");

                        if(key == 'profile_image'){
                            $(errorLabelTitle).insertAfter("#"+key);
                        }
                    });
                }
            },
            complete: function(res){
                $(".submitBtn").attr('disabled', false);
            }
        });
    });

    // Image show in profile page
    $(document).on('change', ".fileInputBoth",function(e){
        var files = e.target.files;
        for (var i = 0; i < files.length; i++) {
            var reader2 = new FileReader();
            reader2.onload = function(e) {
                $('.img-prePro img').attr('src', e.target.result);
                $('.img-prePro img').removeClass('d-none');
                $('x-svg-icons').addClass('d-none');
            };
            reader2.readAsDataURL(files[i]);
        }
    });
</script>
@endsection
