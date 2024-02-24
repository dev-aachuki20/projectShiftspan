@extends('layouts.app')
@section('title', trans('cruds.staff.title_singular'))
@section('customCss')
@endsection

@section('main-content')

    <div class="animate__animated animate__fadeInUp">
        <div class="msg-content white-bg radius-50 space-30 d-flex align-items-center">
            <h2 class="mb-md-0">@lang('cruds.staff.title_singular')</h2>
            @can('staff_create')
                <a href="javascript:void(0)" class="add_btn dash-btn green-bg w-115" id="addStaffBtn" >+ @lang('global.add') @lang('global.new')</a><div class="d-sm-none w-100"></div>
            @endcan
            @can('staff_delete')
                <button class="del_btn dash-btn red-bg w-115 me-md-1" id="deleteAllStaff">@lang('global.delete')</button>
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
    
    @can('staff_create')
        // Add Staff Modal
        $(document).on('click', '#addStaffBtn', function(e){
            e.preventDefault();
            // $('#pageloader').css('display', 'block');
            $.ajax({
                type: 'get',
                url: "{{ route('staffs.create') }}",
                dataType: 'json',
                success: function (response) {
                    // $('#pageloader').css('display', 'none');
                    if(response.success) {
                        $('.popup_render_div').html(response.htmlView);
                        $('#addStaffModal').modal('show');
                    }
                },
                error: function (response) {
                    if(response.responseJSON.error_type == 'something_error'){
                        toasterAlert('error',response.responseJSON.error);
                    } 
                }
            })
        });

        // Submit Add Staff Form
        $(document).on('submit', '#addStaffForm', function (e) {
            e.preventDefault();
            $('.validation-error-block').remove();
            $(".submitBtn").attr('disabled', true);

            var formData = new FormData(this);

            $.ajax({
                type: 'post',
                url: "{{route('staffs.store')}}",
                dataType: 'json',
                contentType: false,
                processData: false,
                data: formData,
                success: function (response) {                
                    if(response.success) {
                        $('#staff-table').DataTable().ajax.reload(null, false);
                        $('#addStaffModal').modal('hide');
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
                            } else if (key.indexOf('staff_name') !== -1) {
                                $(".staff_name_error").html(errorLabelTitle);
                            }
                            else{
                                $(errorLabelTitle).insertAfter("input[name='"+key+"']");
                            }                    
                        });
                    }
                },
                complete: function(res){
                    $(".submitBtn").attr('disabled', false);
                }
            });                    
        });

        $(document).on('submit', '#addNewStaffForm', function (e) {
            e.preventDefault();
            $('.validation-error-block').remove();
            $(".submitBtn").attr('disabled', true);

            var formData = new FormData(this);

            formData.append('save_type', 'new_staff')

            $.ajax({
                type: 'post',
                url: "{{route('staffs.store')}}",
                dataType: 'json',
                contentType: false,
                processData: false,
                data: formData,
                success: function (response) {                
                    if(response.success) {
                        $('#staff-table').DataTable().ajax.reload(null, false);
                        $('#addStaffModal').modal('hide');
                        $('#addNewStaffModal').modal('hide');

                        $('#addNewStaffForm')[0].reset();
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
                            
                            $(errorLabelTitle).insertAfter("input[name='"+key+"']");
                        });
                    }
                },
                complete: function(res){
                    $(".submitBtn").attr('disabled', false);
                }
            });                    
        });
    @endcan

    @can('staff_edit')
        // Edit Staff Modal
        $(document).on("click",".editStaffBtn", function() {
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
                        $('#editStaffModal').modal('show');
                    }
                },
                error: function (response) {
                    if(response.responseJSON.error_type == 'something_error'){
                        toasterAlert('error',response.responseJSON.error);
                    } 
                }
            });
        });
        
        // Submit Edit Staff Form
        $(document).on('submit', '#editStaffForm', function (e) {
            e.preventDefault();
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
                        $('#staff-table').DataTable().ajax.reload(null, false);
                        $('#editStaffModal').modal('hide');
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
                }
            });
        });
    @endcan

    // @can('staff_delete')
    //     $(document).on("click",".deleteStaffBtn", function() {
    //         var url = $(this).data('href');
    //         Swal.fire({
    //             title: "{{ trans('global.areYouSure') }}",
    //             text: "{{ trans('global.onceClickedRecordDeleted') }}",
    //             icon: "warning",
    //             showDenyButton: true,  
    //             //   showCancelButton: true,  
    //             confirmButtonText: "{{ trans('global.swl_confirm_button_text') }}",  
    //             denyButtonText: "{{ trans('global.swl_deny_button_text') }}",
    //         })
    //         .then(function(result) {
    //             if (result.isConfirmed) {  
    //                 $.ajax({
    //                     type: 'DELETE',
    //                     url: url,
    //                     dataType: 'json',
    //                     data: { _token: "{{ csrf_token() }}" },
    //                     success: function (response) {
    //                         if(response.success) {
    //                             $('#staff-table').DataTable().ajax.reload(null, false);
    //                             toasterAlert('success',response.message);
    //                         }
    //                         else {
    //                             toasterAlert('error',response.error);
    //                         }
    //                     },
    //                     error: function(res){
    //                         toasterAlert('error',res.responseJSON.error);
    //                     }
    //                 });
    //             }
    //         });
    //     });

    //     $(document).on('click', '#deleteAllStaff', function(e){
    //         e.preventDefault();
    //         var t = $(this);
    //         var selectedIds = [];
    //         $('.dt_cb:checked').each(function() {
    //             selectedIds.push($(this).data('id'));
    //         });
    //         if(selectedIds.length == 0){
    //             fireWarningSwal('Warning', "{{ trans('messages.warning_select_record') }}");
    //             return false;
    //         }
    //         Swal.fire({
    //             title: "{{ trans('global.areYouSure') }}",
    //             text: "{{ trans('global.onceClickedRecordDeleted') }}",
    //             icon: "warning",
    //             showDenyButton: true,  
    //             //   showCancelButton: true,  
    //             confirmButtonText: "{{ trans('global.swl_confirm_button_text') }}",  
    //             denyButtonText: "{{ trans('global.swl_deny_button_text') }}",
    //         })
    //         .then(function(result) {
    //             if (result.isConfirmed) {
    //                 $.ajax({
    //                     url: "{{route('staffs.massDestroy')}}",
    //                     type: "POST",
    //                     data: { 
    //                         ids: selectedIds,
    //                         _token: "{{ csrf_token() }}",
    //                     },
    //                     dataType: 'json',
    //                     success: function (response) {
    //                         if(response.success) {
    //                             $('#staff-table').DataTable().ajax.reload(null, false);
    //                             toasterAlert('success',response.message);
    //                         }
    //                         else {
    //                             toasterAlert('error',response.error);
    //                         }
    //                     },
    //                     error: function(res){
    //                         toasterAlert('error',res.responseJSON.error);
    //                     }
    //                 })
    //             }
    //         });
    //     })
    // @endcan

    $(document).on("change",".changeStaffStatus", function(e) {
        e.preventDefault();

        var t =$(this);

        var val = t.val();
        var staff_id = t.data('id');
        var old_val = t.data('old_value');

        Swal.fire({
            title: "{{ trans('global.areYouSure') }}",
            text: "{{ trans('global.want_to_change_status') }}",
            icon: "warning",
            showDenyButton: true,  
            //   showCancelButton: true,  
            confirmButtonText: "{{ trans('global.swl_confirm_button_text') }}",  
            denyButtonText: "{{ trans('global.swl_deny_button_text') }}",
        })
        .then(function(result) {
            if (result.isConfirmed) {  
                $.ajax({
                    type: 'post',
                    url: "{{route('staffs.update.status')}}",
                    dataType: 'json',
                    data: { _token: "{{ csrf_token() }}", 'id' : staff_id },
                    success: function (response) {
                        if(response.success) {
                            t.data('old_value', val);
                            $('#staff-table').DataTable().ajax.reload(null, false);
                            toasterAlert('success',response.message);
                        }
                        else {
                            t.val(old_val)
                            toasterAlert('error',response.error);
                        }
                    },
                    error: function(res){
                        t.val(old_val)
                        toasterAlert('error',res.responseJSON.error);
                    }
                });
            } else {
                t.val(old_val)
            }
        });
    });
</script>

@endsection
