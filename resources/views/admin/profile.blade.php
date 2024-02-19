@extends('layouts.app')
@section('title', trans('cruds.user.admin_profile.title'))
@section('customCss')

@endsection

@section('main-content')

<div class="animate__animated animate__fadeInUp">
    <div class="msg-content white-bg radius-50 space-30 d-flex align-items-center">
        <h2 class="mb-md-0">@lang('global.update') @lang('cruds.user.admin_profile.title')</h2>
    </div>
    <div class="profile-form mw-820 mx-auto pt-5 modal-size-l">
        <form class="msg-form" id="profile-form" enctype="multipart/form-data">
            @csrf
            <div class="form-label">
                <label>@lang('cruds.user.admin_profile.fields.admin_name'):</label>
                <input type="text" name="name" value="{{$user->name}}">
            </div>
            <div class="form-label">
                <label>@lang('cruds.user.fields.email'):</label>
                <input type="email" disabled value="{{$user->email}}"/>
            </div>
            <div class="form-label">
                <label>@lang('cruds.user.admin_profile.fields.mobile'):</label>
                <input type="text" name="phone" value="{{$user->phone ?? ''}}">
            </div>
            <div class="form-label">
                <label>@lang('cruds.user.admin_profile.fields.image'):</label>
                <div class="right-sidebox">
                    <div class="img-prevarea img-prePro">
                        
                        {{-- @if($user->profile_image_url)
                            <img src="{{$user->profile_image_url}}" alt="profile image">
                        @else
                            <img src="" alt="profile image" class="d-none">
                            <x-svg-icons icon="default-user" />
                        @endif --}}
                        <img src="{{ $user->profile_image_url ? $user->profile_image_url : asset(config('constant.default.user_icon')) }}" >
                    </div>
                    <div class="chose-btn-area position-relative">
                        <a href="javascript:void(0)" class="chose-btn">@lang('global.choose') @lang('cruds.user.admin_profile.fields.image')</a>
                        <input type="file" id="image-input" name="profile_image" class="fileInputBoth" accept="image/*">
                    </div>
                </div>
            </div>
            <div class="form-label justify-content-center">
                <button type="submit" class="cbtn submitBtn">@lang('global.update') @lang('cruds.user.admin_profile.title')</button>
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
                    toasterAlert('success',response.message);                    
                    updateHeaderProfile(response.profile_image, response.auth_name);
                }
            },
            error: function (response) {
                console.log(response);
                if(response.responseJSON.error_type == 'something_error'){
                    toasterAlert('error',response.responseJSON.error);
                } else {                    
                    var errorLabelTitle = '';
                    $.each(response.responseJSON.errors, function (key, item) {
                        errorLabelTitle = '<span class="validation-error-block">'+item[0]+'</sapn>';
                        
                        $(errorLabelTitle).insertAfter("input[name='"+key+"']");

                        /* if(key == 'profile_image'){
                            $(errorLabelTitle).insertAfter("#"+key);
                        } */
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
