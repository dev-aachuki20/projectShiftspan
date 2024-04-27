@extends('layouts.app')
@section('title', trans('cruds.setting.title_singular'))
@section('customCss')

@endsection

@section('main-content')
    <div class="animate__animated animate__fadeInUp">

        <div class="row">
            <div class="col-md-6">
                <div class="msg-content white-bg radius-50 space-30 d-flex align-items-center">
                    <h2 class="mb-0">@lang('global.update') @lang('cruds.setting.title')</h2>
                </div>
                <div class="mw-820 mx-auto pt-4 modal-size-l">
                    @include('admin.setting.form')
                </div>
            </div>
            <div class="col-md-6 pt-5 pt-md-0">
                <div class="msg-content white-bg radius-50 space-30 d-flex align-items-center">
                    <h2 class="mb-0">@lang('cruds.setting.add_message_subject')</h2>
                </div>

                <div class="mw-820 mx-auto pt-4 modal-size-l add-msg-subject">
                    @can('setting_message_subject_create')
                    <form action="" class="msg-form" id="AddSubjectForm">
                        @csrf
                        <div class="form-label">
                            <label for="">@lang('cruds.setting.message_subject.subject_name'):</label>
                            <input type="text" id="subject_name" name="subject_name" />
                            <input type="submit" value="@lang('global.add')" class="cbtn addNewSubjectBtn">
                        </div>
                    </form>
                    @endcan

                    <div class="table-responsive mb-0">
                        <table class="table common-table short-table nowrap">
                            <thead>
                                <tr>
                                    <th>@lang('global.sno')</th>
                                    <th>@lang('cruds.setting.message_subject.subject_name')</th>
                                    <th>@lang('global.action')</th>
                                </tr>
                            </thead>
                            <tbody>

                                @if (getSetting('message_subject'))
                                    @foreach (getSetting('message_subject') as $index => $value)
                                        <tr>
                                            <td>{{ ++$index }}</td>
                                            <td>{{ $value ?? 0 }}</td>
                                            <td>
                                                @can('setting_message_subject_delete')
                                                <button class="dash-btn red-bg small-btn icon-btn deleteSubjectBtn" data-href="{{ route('settings.subject.delete') }}" data-deleteid="{{ encrypt($value) }}">
                                                <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="@lang('global.delete')">
                                                    <x-svg-icons icon="delete" />
                                                </span>
                                                </button>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3">@lang('messages.no_record_found')</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('customJS')
<script>
$(document).ready(function(){

    $(document).on('submit', '#settingform', function(e){
        e.preventDefault();
        $('.loader-div').show();
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
            };
            reader2.readAsDataURL(files[i]);
        }
    });

    // Add New Message Subject
    $(document).on('submit',"#AddSubjectForm",function(e){
        e.preventDefault();
        $('.loader-div').show();
        $(".addNewSubjectBtn").attr('disabled', true);
        $('.validation-error-block').remove();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ route('settings.subject.store') }}",
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
                        errorLabelTitle = '<p class="validation-error-block text-danger">'+item[0]+'</p>';
                        var inputField = $("input[name='"+key+"']");
                        var parentElement = inputField.parent();
                        $(errorLabelTitle).insertAfter(parentElement);
                    });
                }
            },
            complete: function(res){
                $(".addNewSubjectBtn").attr('disabled', false);
                $('.loader-div').hide();
            }
        });

    });

    $(document).on("click",".deleteSubjectBtn", function(e) {
        e.preventDefault();
        var hrefurl = $(this).data('href');
        var data = $(this).attr('data-deleteid');
        Swal.fire({
            title: "{{ trans('global.areYouSure') }}",
            text: "{{ trans('global.onceClickedRecordDeleted') }}",
            icon: "warning",
            showDenyButton: true,
            //   showCancelButton: true,
            confirmButtonText: "{{ trans('global.swl_confirm_button_text') }}",
            denyButtonText: "{{ trans('global.swl_deny_button_text') }}",
        })
        .then(function(result) {
            if (result.isConfirmed) {
                $('.loader-div').show();
                $.ajax({
                    type: 'POST',
                    url: hrefurl,
                    dataType: 'json',
                    data: { _token: "{{ csrf_token() }}",data : data },
                    success: function (response) {
                        if(response.success) {
                            toasterAlert('success',response.message);
                            $('.loader-div').hide();
                            location.reload();
                        }
                        else {
                            toasterAlert('error',response.error);
                            $('.loader-div').hide();
                        }
                    },
                    error: function(res){
                        toasterAlert('error',res.responseJSON.error);
                    }
                });
            }
        });
    });
});
</script>
@endsection
