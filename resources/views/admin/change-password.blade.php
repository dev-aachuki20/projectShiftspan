@extends('layouts.app')
@section('title', trans('global.change_password'))
@section('customCss')

@endsection

@section('main-content')
    <div class="animate__animated animate__fadeInUp">
        <div class="msg-content white-bg radius-50 space-30 d-flex align-items-center">
            <h2 class="mb-0">@lang('global.change_password')</h2>
        </div>
        <div class="profile-form mw-820 mx-auto pt-5 modal-size-l">
            <form class="msg-form" id="change-password-form">
                @csrf
                <div class="form-label password-area">
                    <label>@lang('global.old_password'):</label>
                    <input type="password" name="currentpassword" value="">
                    <span class="toggle-password close-eye">
                        <x-svg-icons icon="close-eye" />
                        <x-svg-icons icon="open-eye" />
                    </span>
                </div>
                <div class="form-label password-area">
                    <label>@lang('global.new_password'):</label>
                    <input type="password" name="password" value="">

                    <span class="toggle-password close-eye">
                        <x-svg-icons icon="close-eye" />
                        <x-svg-icons icon="open-eye" />
                    </span>
                </div>
                <div class="form-label password-area">
                    <label>@lang('global.confirm_password'):</label>
                    <input type="password" name="password_confirmation" value="">

                    <span class="toggle-password close-eye">
                        <x-svg-icons icon="close-eye" />
                        <x-svg-icons icon="open-eye" />
                    </span>
                </div>
                <div class="form-label justify-content-center">
                    <input type="submit" value="@lang('global.change_password')" class="cbtn submitBtn">
                </div>
            </form>
        </div>
    </div>
@endsection
@section('customJS')
<script>
    $(document).on('submit', '#change-password-form', function(e){
        e.preventDefault();
        $(".submitBtn").attr('disabled', true);

        $('.validation-error-block').remove();

        var formData = new FormData(this);

        $.ajax({
            type: 'post',
            url: "{{ route('update.change.password') }}",
            dataType: 'json',
            contentType: false,
            processData: false,
            data: formData,
            success: function (response) {
                if(response.success) {
                    $('#change-password-form')[0].reset();
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
                        errorLabelTitle = '<span class="validation-error-block">'+item[0]+'</sapn>';
                        
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
</script>
@endsection