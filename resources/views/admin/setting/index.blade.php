@extends('layouts.app')
@section('title', trans('cruds.setting.title_singular'))
@section('customCss')

@endsection

@section('main-content')
    <div class="animate__animated animate__fadeInUp">
        <div class="msg-content white-bg radius-50 space-30 d-flex align-items-center">
            <h2 class="mb-0">@lang('global.update') @lang('cruds.setting.title_singular')</h2>
        </div>
        <div class="profile-form mw-820 mx-auto pt-5 modal-size-l">
            @include('admin.setting.form')
        </div>
    </div>
@endsection


@section('customJS')
<script>
$(document).ready(function(){

    $(document).on('submit', '#settingform', function(e){
        e.preventDefault();
        $(".submitBtn").attr('disabled', true);

        $('.validation-error-block').remove();

        var formData = new FormData(this);

        $.ajax({
            type: 'post',
            url: "{{ route('update.setting') }}",
            dataType: 'json',
            contentType: false,
            processData: false,
            data: formData,
            success: function (response) {
                if(response.success) {
                    toasterAlert('success',response.message);
                    window.location.reload();
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
            };
            reader2.readAsDataURL(files[i]);
        }
    });
});
</script>
@endsection
