@extends('layouts.app')
@section('title', trans('cruds.client_admin.title_singular'))
@section('customCss')
@endsection

@section('main-content')

    <div class="animate__animated animate__fadeInUp">
        <div class="msg-content white-bg radius-50 space-30 d-flex align-items-center">
            <h2 class="mb-md-0">@lang('cruds.client_admin.title')</h2>
            @can('sub_admin_create')
                <a href="javascript:void(0)" class="add_btn dash-btn green-bg w-115" id="addSubAdminBtn" >+ @lang('global.add') @lang('global.new')</a><div class="d-sm-none w-100"></div>
            @endcan
            @can('sub_admin_delete')
                <button class="del_btn dash-btn red-bg w-115 me-md-1" id="deleteAllSubAdmin">@lang('global.delete')</button>
            @endcan
        </div>
        <div class="c-admin position-relative">
            <div class="table-responsive">
                {{$dataTable->table(['class' => 'table common-table short-table nowrap', 'style' => 'width:100%;'])}}
            </div>
        </div>
    </div>

@endsection
@section('customJS')

@parent
{!! $dataTable->scripts() !!}

<script>
@can('sub_admin_create')
    // Add SubAdmin Modal
    $(document).on('click', '#addSubAdminBtn', function(e){
        e.preventDefault();
        $('.loader-div').show();
        $.ajax({
            type: 'get',
            url: "{{ route('client-admins.create') }}",
            dataType: 'json',
            success: function (response) {
                if(response.success) {
                    $('.popup_render_div').html(response.htmlView);
                    $('#addSubAdminModal').modal('show');
                    $('.loader-div').hide();
                }
            },
            error: function (response) {
                if(response.responseJSON.error_type == 'something_error'){
                    toasterAlert('error',response.responseJSON.error);
                } 
            }
        })
    });

    // Submit Add SubAdmin Form
    $(document).on('submit', '#addSubAdminForm', function (e) {
        e.preventDefault();
        $('.loader-div').show();

        $('.validation-error-block').remove();
        $(".submitBtn").attr('disabled', true);

        var formData = new FormData(this);

        $.ajax({
            type: 'post',
            url: "{{route('client-admins.store')}}",
            dataType: 'json',
            contentType: false,
            processData: false,
            data: formData,
            success: function (response) {
                if(response.success) {
                    $('#client-admin-table').DataTable().ajax.reload(null, false);
                    $('#addSubAdminModal').modal('hide');
                    $('.popup_render_div').html('');
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
                        if (key.indexOf('sub_admin') !== -1) {
                            $(".sub_admin_error").html(errorLabelTitle);
                        }
                        else{
                            $(errorLabelTitle).insertAfter("input[name='"+key+"']");
                        }                    
                    });
                }
            },
            complete: function(res){
                $(".submitBtn").attr('disabled', false);
                $('.loader-div').hide();
            }
        });                    
    });
@endcan

@can('sub_admin_edit')
    // Edit SubAdmin Modal
    $(document).on("click",".editSubAdminBtn", function (e) {
        e.preventDefault();
        $('.loader-div').show();
        // $('#pageloader').css('display', 'flex');
        var url = $(this).data('href');
        $.ajax({
            type: 'get',
            url: url,
            dataType: 'json',
            //data: formData,
            success: function (response) {
                // $('#pageloader').css('display', 'none');
                if(response.success) {
                    $('.popup_render_div').html(response.htmlView);
                    $('#editSubAdminModal').modal('show');
                    $('.loader-div').hide();
                }
            },
            error: function (response) {
                if(response.responseJSON.error_type == 'something_error'){
                    toasterAlert('error',response.responseJSON.error);
                } 
            }
        });
    });
    
    // Submit Edit SubAdmin Form
    $(document).on('submit', '#editSubAdminForm', function (e) {
        e.preventDefault();
        $('.loader-div').show();

        $('.validation-error-block').remove();
        $(".submitBtn").attr('disabled', true);
        var formData = new FormData(this);

        var url = $(this).data('action');

        $.ajax({
            type: 'post',
            url: url,
            dataType: 'json',
            contentType: false,
            processData: false,
            data: formData,
            success: function (response) {
                if(response.success) {
                    $('#client-admin-table').DataTable().ajax.reload(null, false);
                    $('#editSubAdminModal').modal('hide');
                    toasterAlert('success',response.message);
                }
            },
            error: function (response) {
                $(".submitBtn").attr('disabled', false);
                if(response.responseJSON.error_type == 'something_error'){
                    toasterAlert('error',response.responseJSON.error);
                } else {                    
                    var errorLabelTitle = '';
                    $.each(response.responseJSON.errors, function (key, item) {
                        errorLabelTitle = '<span class="validation-error-block">'+item+'</sapn>';
                        if (key.indexOf('sub_admin') !== -1) {
                            $(".sub_admin_error").html(errorLabelTitle);
                        } else {
                            $(errorLabelTitle).insertAfter("input[name='"+key+"']");
                        }
                    });
                }
            },
            complete: function(res){
                $(".submitBtn").attr('disabled', false);
                $('.loader-div').hide();
            }
        });
    });
@endcan

@can('sub_admin_delete')
    $(document).on("click",".deleteSubAdminBtn", function() {
        var url = $(this).data('href');
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
                    type: 'DELETE',
                    url: url,
                    dataType: 'json',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function (response) {
                        if(response.success) {
                            $('#client-admin-table').DataTable().ajax.reload(null, false);
                            toasterAlert('success',response.message);
                            $('.loader-div').hide();
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

    $(document).on('click', '#deleteAllSubAdmin', function(e){
        e.preventDefault();
        var t = $(this);
        var selectedIds = [];
        $('.dt_cb:checked').each(function() {
            selectedIds.push($(this).data('id'));
        });
        if(selectedIds.length == 0){
            fireWarningSwal('Warning', "{{ trans('messages.warning_select_record') }}");
            return false;
        }
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
                    url: "{{route('client-admins.massDestroy')}}",
                    type: "POST",
                    data: { 
                        ids: selectedIds,
                        _token: "{{ csrf_token() }}",
                    },
                    dataType: 'json',
                    success: function (response) {
                        if(response.success) {
                            $('#client-admin-table').DataTable().ajax.reload(null, false);
                            toasterAlert('success',response.message);
                            $('.loader-div').hide();
                        }
                        else {
                            toasterAlert('error',response.error);
                            $('.loader-div').hide();
                        }
                    },
                    error: function(res){
                        toasterAlert('error',res.responseJSON.error);
                    }
                })
            }
        });
    })
@endcan

@can('sub_admin_edit')
    $(document).ready(function() {
        $(document).on('click', '.custom-select', function() {
            // $(".custom-select.custom-dropdown").removeClass("custom-dropdown");
            
            $(this).toggleClass('custom-dropdown');
            //$('.custom-select').removeClass('custom-dropdown');
            
        });
    });

    $(document).on("click",".changeSubAdminStatus", function(e) {
        e.preventDefault();

        var t =$(this);

        var old_val     = t.data('selected_value');
        var staff_id    = t.data('id');
        var status_val  = t.data('val');
        var selectedText  = t.text();

        if(old_val == status_val){
            return false;
        }

        Swal.fire({
            title: "{{ trans('global.areYouSure') }}",
            text: "{{ trans('global.want_to_change_status') }}",
            icon: "warning",
            showDenyButton: true,   
            confirmButtonText: "{{ trans('global.swl_confirm_button_text') }}",  
            denyButtonText: "{{ trans('global.swl_deny_button_text') }}",
        })
        .then(function(result) {
            if (result.isConfirmed) {  
                $('.loader-div').show();
                $.ajax({
                    type: 'post',
                    url: "{{route('client-admins.statusUpdate')}}",
                    dataType: 'json',
                    data: { _token: "{{ csrf_token() }}", 'id' : staff_id },
                    success: function (response) {
                        if(response.success) {
                            t.closest(".custom-select").find('.main-select-box').text(selectedText);
                            t.closest(".select-options").slideUp();

                            $('#client-admin-table').DataTable().ajax.reload(null, false);
                            toasterAlert('success',response.message);
                            // t.closest(".custom-select").removeClass('custom-dropdown');
                        }
                        else {
                            t.val(old_val)
                            toasterAlert('error',response.error);
                        }
                        $('.loader-div').hide();
                        t.closest(".custom-select").removeClass('custom-dropdown');
                    },
                    error: function(res){
                        toasterAlert('error',res.responseJSON.error);
                        $('.loader-div').hide();
                    }
                });
            }
        });
    });
@endcan

</script>

@endsection