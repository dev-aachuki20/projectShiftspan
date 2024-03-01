@extends('layouts.app')
@section('title', trans('cruds.setting.contact_details.title'))
@section('customCss')

@endsection

@section('main-content')

<div class="animate__animated animate__fadeInUp">
    <div class="msg-content white-bg radius-50 space-30 d-flex align-items-center">
        <h2 class="mb-0">@lang('cruds.setting.contact_details.title')</h2>
    </div>
    <div class="profile-form mw-820 mx-auto pt-5 modal-size-l">
        <form class="msg-form" id="profile-form" enctype="multipart/form-data">
            @csrf
            @foreach($settings as $key => $setting)
                @if($setting->type == 'text')
                    <div class="form-label">
                        <label>{{$setting->display_name}}:</label>
                        <input type="text" value="{{$setting->value}}" name="{{$setting->key}}" />
                    </div>
                @endif
            @endforeach
            <div class="form-label justify-content-center">
                <button type="submit" class="cbtn submitBtn">@lang('cruds.setting.contact_details.fields.contact_details')</button>
            </div>
        </form>
    </div>
</div>

@endsection
@section('customJS')
<script>
    $(document).on('submit', '#profile-form', function(e){
        e.preventDefault();
        $('.loader-div').show();

        $(".submitBtn").attr('disabled', true);

        $('.validation-error-block').remove();

        var formData = new FormData(this);

        $.ajax({
            type: 'post',
            url: "{{ route('update.contact-detail') }}",
            dataType: 'json',
            contentType: false,
            processData: false,
            data: formData,
            success: function (response) {
                if(response.success) {
                    toasterAlert('success',response.message);
                }
            },
            error: function (response) {
                if(response.responseJSON.error_type == 'something_error'){
                    toasterAlert('error',response.responseJSON.error);
                } else {                    
                    var errorLabelTitle = '';
                    $.each(response.responseJSON.errors, function (key, item) {
                        errorLabelTitle = '<span class="validation-error-block">'+item[0]+'</sapn>';
                        console.log("input[name='"+key+"']");
                        $("input[name='"+key+"']").after(errorLabelTitle);
                    });
                }
            },
            complete: function(res){
                $(".submitBtn").attr('disabled', false);
                $('.loader-div').hide();
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
