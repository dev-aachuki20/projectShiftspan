@extends('layouts.app')
@section('title')@lang('cruds.setting.title_singular')@endsection
@section('customCss')

@endsection

@section('main-content')
    <div class="animate__animated animate__fadeInUp">
        <div class="msg-content white-bg radius-50 space-30 d-flex align-items-center">
            <h2 class="mb-md-0">@lang('global.update') @lang('cruds.setting.title_singular')</h2>
        </div>
        <div class="profile-form mw-820 mx-auto pt-5 modal-size-l">
            @include('admin.setting.form')
        </div>
    </div>
@endsection


@section('customJS')
<script src="{{ asset('admintheme/assets/bundles/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('admintheme/assets/bundles/summernote/summernote-bs4.min.js') }}"></script>

<script>
$(document).ready(function(){

    $(document).on('submit','#settingform',function(e){
        e.preventDefault();
        $("#settingform button[type=submit]").prop('disabled',true);
        $(".error").remove();
        $(".is-invalid").removeClass('is-invalid');
        var formData = new FormData(this);
        var formAction = $(this).attr('action');
        

        $.ajax({
            type: "POST",
            url: formAction,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                    var alertType = response['alert-type'];
                    var message = response['message'];
                    var title = "{{ trans('quickadmin.settings.title') }}";
                    showToaster(title,alertType,message);
                    $('#settingform')[0].reset();
                    location.reload();
                    $("#settingform button[type=submit]").prop('disabled',false);
            },
            error: function (xhr) {
                var errors= xhr.responseJSON.errors;
                console.log(xhr.responseJSON);

                for (const elementId in errors) {
                    $("#settingform #"+elementId).addClass('is-invalid');
                    var errorHtml = '<span class="error text-danger">'+errors[elementId]+'</span>';
                    $(errorHtml).insertAfter($("#settingform #"+elementId));
                }
                $("#settingform button[type=submit]").prop('disabled',false);
            }
        });
    });

    $(document).on('click','.copy-btn',function(event){
        event.preventDefault();
        var elementVal = $(this).attr('data-elementVal');
        var targetTextareaId = $(this).attr('data-targetTextareaId');
        $('#' + targetTextareaId).summernote('insertText', elementVal);
    });
});
</script>
@endsection
