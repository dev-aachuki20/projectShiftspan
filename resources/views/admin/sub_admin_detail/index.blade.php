@extends('layouts.app')
@section('title', trans('cruds.client_detail.title_singular'))

@section('customCss')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('main-content')
    <div class="animate__animated animate__fadeInUp">
        <div class="msg-content white-bg radius-50 space-30 d-flex align-items-center">
            <h2 class="mb-md-0">@lang('cruds.client_detail.title_singular')</h2>
            @can('sub_admin_detail_create')
                <a href="javascript:void(0)" class="add_btn dash-btn green-bg w-115" id="addSubAdminDetailBtn" >+ @lang('global.add') @lang('global.new')</a><div class="d-sm-none w-100"></div>
            @endcan
            @can('sub_admin_detail_delete')
                <button class="del_btn dash-btn red-bg w-115 me-md-1" id="deleteAllSubAdminDetail">@lang('global.delete')</button>
            @endcan
        </div>
        <div class="c-admin position-relative">
            <div >
                {{$dataTable->table(['class' => 'table common-table short-table nowrap', 'style' => 'width:100%;'])}}
            </div>
        </div>
    </div>
@endsection

@section('customJS')

@parent
{!! $dataTable->scripts() !!}

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).on('shown.bs.modal', function() {
        $(".select2").select2({
            dropdownParent: $('.select-label'),
            selectOnClose: false
        });
    });
    @can('sub_admin_detail_create')
        // Add Client Details Modal
        $(document).on('click', '#addSubAdminDetailBtn', function(e){
            e.preventDefault();
            $('.loader-div').show();
            $.ajax({
                type: 'get',
                url: "{{ route('client-details.create') }}",
                dataType: 'json',
                success: function (response) {
                    if(response.success) {
                        $('.popup_render_div').html(response.htmlView);
                        $('#addSubAdminDetailModal').modal('show');
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

        // Submit Add Client Details Form
        $(document).on('submit', '#addSubAdminDetailForm', function (e) {
            e.preventDefault();
            $('.loader-div').show();
            
            $('.validation-error-block').remove();
            $(".submitBtn").attr('disabled', true);

            var formData = new FormData(this);

            $.ajax({
                type: 'post',
                url: "{{route('client-details.store')}}",
                dataType: 'json',
                contentType: false,
                processData: false,
                data: formData,
                success: function (response) {                
                    if(response.success) {
                        $('#client-detail-table').DataTable().ajax.reload(null, false);
                        $('#addSubAdminDetailModal').modal('hide');
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
                            if (key.indexOf('sub_admin_id') !== -1) {
                                $(".client_name_error").html(errorLabelTitle);
                            } else if(key == 'building_image'){
                                $(".building_image_error").html(errorLabelTitle);
                            }
                            else{
                                $(errorLabelTitle).insertAfter("input[name='"+key+"']");
                                $(errorLabelTitle).insertAfter("textarea[name='"+key+"']");
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

    @can('sub_admin_detail_edit')
        // Edit Client Detail Modal
        $(document).on("click",".editSubAdminDetailBtn", function(e) {
            e.preventDefault();
            $('.loader-div').show();
            // $('#pageloader').css('display', 'flex');
            $('#viewSubAdminDetailModal').modal('hide');
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
                        
                        $('#editSubAdminDetailModal').modal('show');
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
        
        // Submit Edit Client Detail Form
        $(document).on('submit', '#editSubAdminDetailForm', function (e) {
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
                        $('#client-detail-table').DataTable().ajax.reload(null, false);
                        $('#editSubAdminDetailModal').modal('hide');
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
    
    @can('sub_admin_detail_view')
        $(document).on("click",".viewSubAdminDetailBtn", function() {
            $('.loader-div').show();

            var url = $(this).data('href');
            $.ajax({
                type: 'get',
                url: url,
                dataType: 'json',
                success: function (response) {
                    if(response.success) {
                        $('.popup_render_div').html(response.htmlView);
                        $('#viewSubAdminDetailModal').modal('show');
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
    @endcan

    @can('sub_admin_detail_delete')
        $(document).on("click",".deleteSubAdminDetailBtn", function() {
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
                                $('#client-detail-table').DataTable().ajax.reload(null, false);
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

        $(document).on('click', '#deleteAllSubAdminDetail', function(e){
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
                        url: "{{route('client-details.massDestroy')}}",
                        type: "POST",
                        data: { 
                            ids: selectedIds,
                            _token: "{{ csrf_token() }}",
                        },
                        dataType: 'json',
                        success: function (response) {
                            if(response.success) {
                                $('#client-detail-table').DataTable().ajax.reload(null, false);
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
