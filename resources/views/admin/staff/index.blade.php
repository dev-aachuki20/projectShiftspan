@extends('layouts.app')
@section('title', trans('cruds.staff.title_singular'))

@section('customCss')
<link href="{{asset('plugins/jquery-ui/jquery.ui.min.css')}}" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
            @can('notification_create')
                <button class="dash-btn blue-bg w-115 ms-sm-2 mt-2 mt-sm-0" id="notificationSettings" {{-- onclick="NnotificationSettings()" --}}>Send Notification</button>
            @endcan
        </div>
        <div class="c-admin position-relative">
            <div class="table-responsive">
                {{$dataTable->table(['class' => 'table common-table short-table nowrap', 'style' => 'width:100%;'])}}
            </div>
        </div>
    </div>

    {{-- Notification model --}}
    {{-- @include('admin.staff.notification.create') --}}
@endsection

@section('customJS')

@parent
{!! $dataTable->scripts() !!}
<script src="{{asset('plugins/jquery-ui/jquery.ui.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $(document).on('click', '.custom-select', function() {
            
           if ($(this).hasClass('custom-dropdown')) {
                $(this).removeClass('custom-dropdown');
            } else {
                $(".custom-select.custom-dropdown").removeClass("custom-dropdown");
                $(this).addClass('custom-dropdown');
            }
            
        });
    });

    $(document).on('shown.bs.modal', function() {
        $('.select2').each(function() {
            if (this.isConnected) {
                $(this).select2({
                    width: 'calc(100% - 180px)',
                    dropdownParent: $(this).closest('.select-label'),
                    selectOnClose: false
                });
            }
        });

        $('.staff-checkbox').on('change', function() {
            selectedStaffCheckboxes();
        });

        $(document).on('click','.selectAllStaff',function(event) {
        
            var $checkboxes = $('.options input[type="checkbox"]');
            var allChecked = $checkboxes.filter(':checked').length === $checkboxes.length;
            $checkboxes.prop('checked', !allChecked);

            selectedStaffCheckboxes();

        });

        $(function () {
            $(".datepicker").datepicker({
                maxDate: 0,
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                yearRange: "-100:+0",
                // showButtonPanel: true,
            });
        });
    });

    /* $(document).ready(function(){
        $('#searchInput').on('input', function() {
            var searchText = $(this).val().toLowerCase();
            $('.custom-check li').each(function() {
                var text = $(this).text().toLowerCase();
                if (text.includes(searchText)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    }); */

    function searchInput(){
        $('#searchInput').on('input', function() {
            var searchText = $(this).val().toLowerCase();
            $('.custom-check li').each(function() {
                var text = $(this).text().toLowerCase();
                if (text.includes(searchText)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    }

    
    @can('staff_create')
        // Add Staff Modal
        $(document).on('click', '#addStaffBtn', function(e){
            e.preventDefault();
            // $('#pageloader').css('display', 'block');
            $('.loader-div').show();
            $.ajax({
                type: 'get',
                url: "{{ route('staffs.create') }}",
                dataType: 'json',
                success: function (response) {
                    // $('#pageloader').css('display', 'none');
                    if(response.success) {
                        $('.popup_render_div').html(response.htmlView);
                        $('#addStaffModal').modal('show');
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

        // Submit Add Staff Form
        $(document).on('submit', '#addStaffForm', function (e) {
            e.preventDefault();
            $('.loader-div').show();

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
                            if (key.indexOf('company_id') !== -1) {
                                $(".sub_admin_error").html(errorLabelTitle);
                            } else{
                                $(document).find('[name='+key+']').after(errorLabelTitle);
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

        $(document).on('submit', '#addNewStaffForm', function (e) {
            e.preventDefault();
            $('.loader-div').show();

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
                        $('.loader-div').hide();
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
                            /* errorLabelTitle = '<span class="validation-error-block">'+item[0]+'</sapn>';
                            
                            $(errorLabelTitle).insertAfter("input[name='"+key+"']"); */
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
            $('#staffDetails').modal('hide');

            $('.loader-div').show();
            
            var url = $(this).data('href');
            $.ajax({
                type: 'get',
                url: url,
                dataType: 'json',
                success: function (response) {
                    if(response.success) {
                        $('.popup_render_div').html(response.htmlView);
                        $('#editStaffModal').modal('show');
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
        
        // Submit Edit Staff Form
        $(document).on('submit', '#editStaffForm', function (e) {
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
                            if (key.indexOf('company_id') !== -1) {
                                $(".sub_admin_error").html(errorLabelTitle);
                            } else{
                                $(document).find('[name='+key+']').after(errorLabelTitle);
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

    @can('staff_delete')
        $(document).on("click",".deleteStaffBtn", function() {
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
                                $('#staff-table').DataTable().ajax.reload(null, false);
                                toasterAlert('success',response.message);
                            }
                            else {
                                toasterAlert('error',response.error);
                            }
                            $('.loader-div').hide();
                        },
                        error: function(res){
                            toasterAlert('error',res.responseJSON.error);
                            $('.loader-div').hide();
                        }
                    });
                }
            });
        });

        $(document).on('click', '#deleteAllStaff', function(e){
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
                        url: "{{route('staffs.massDestroy')}}",
                        type: "POST",
                        data: { 
                            ids: selectedIds,
                            _token: "{{ csrf_token() }}",
                        },
                        dataType: 'json',
                        success: function (response) {
                            if(response.success) {
                                $('#staff-table').DataTable().ajax.reload(null, false);
                                setTimeout(() => {
                                    $('#dt_cb_all').prop('checked', false);
                                }, 500);
                                toasterAlert('success',response.message);
                            }
                            else {
                                toasterAlert('error',response.error);
                            }
                            $('.loader-div').hide();
                        },
                        error: function(res){
                            toasterAlert('error',res.responseJSON.error);
                            $('.loader-div').hide();
                        }
                    })
                }
            });
        })
    @endcan

    $(document).on("click",".changeStaffStatus", function(e) {
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
            //   showCancelButton: true,  
            confirmButtonText: "{{ trans('global.swl_confirm_button_text') }}",  
            denyButtonText: "{{ trans('global.swl_deny_button_text') }}",
        })
        .then(function(result) {
            if (result.isConfirmed) {  
                $('.loader-div').show();
                $.ajax({
                    type: 'post',
                    url: "{{route('staffs.update.status')}}",
                    dataType: 'json',
                    data: { _token: "{{ csrf_token() }}", 'id' : staff_id },
                    success: function (response) {
                        if(response.success) {
                            t.closest(".custom-select").find('.main-select-box').text(selectedText);
                            t.closest(".select-options").slideUp();

                            $('#staff-table').DataTable().ajax.reload(null, false);
                            toasterAlert('success',response.message);

                            $('.loader-div').hide();
                        }
                        else {
                            t.val(old_val)
                            toasterAlert('error',response.error);
                        }
                    },
                    error: function(res){
                        toasterAlert('error',res.responseJSON.error);
                    }
                });
            }
        });
    });

    /* Notification */
    $(document).on("click","#notificationSettings", function() {
        $('.loader-div').show();

        $.ajax({
            type: 'get',
            url: "{{route('staffs.createNotification')}}",
            dataType: 'json',
            success: function (response) {
                if(response.success) {
                    $('.popup_render_div').html(response.htmlView);
                    $('#NnotificationSettings').modal('show');
                    searchInput();
                    $('.loader-div').hide();
                }
            },
            error: function (response) {
                if(response.responseJSON.error_type == 'something_error'){
                    toasterAlert('error',response.responseJSON.error);
                    $('.loader-div').hide();
                } 
            }
        });
    });
    
    $(document).on('submit', '#addNotificationForm', function (e) {
        e.preventDefault();
        $('.validation-error-block').remove();
        $(".submitBtn").attr('disabled', true);

        var formData = new FormData(this);

        $.ajax({
            type: 'post',
            url: "{{route('staffs.notificationStore')}}",
            dataType: 'json',
            contentType: false,
            processData: false,
            data: formData,
            success: function (response) {                
                if(response.success) {
                    // console.log(response.message);
                    $('#addNotificationForm')[0].reset();
                    $('#NnotificationSettings').modal('hide');
                    
                    var selected = [];
                    selected.push($(this).closest(".select-option").find('span').text());
                    $('.selected-options').text(selected.length > 0 ? 'Select...' : 'Select...');

                    $('.popup_render_div').html('');
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
                        errorLabelTitle = '<span class="validation-error-block">'+item[0]+'</sapn>'; 
                        if (key.indexOf('staffs') !== -1) {
                            $(".staffs_error").html(errorLabelTitle);
                        } else if(key== 'section') {
                            $(".section_error").html(errorLabelTitle);
                        } else{
                            $(document).find('[name='+key+']').after(errorLabelTitle);
                        }
                    });
                }
            },
            complete: function(res){
                $(".submitBtn").attr('disabled', false);
            }
        });                    
    });

    $('.btn-close').click(function() {
        $('.validation-error-block').remove();
    });

    function selectedStaffCheckboxes(){
        var selectedDataArray = [];
        var checkedCheckboxes = $(document).find(".staff-checkbox:checked");
        checkedCheckboxes.each(function(){
            var dataValue = $(this).attr("data-company");
            if (!selectedDataArray.includes(dataValue)) {
                selectedDataArray.push(dataValue);
            }
        });
       
        if(selectedDataArray.length > 0){
            var hiddenInputs = ''; 
            selectedDataArray.forEach(function(id) {
                hiddenInputs += '<input type="hidden" name="companies[]" value="' + id + '" id="companyUUId_' + id + '">';
            });
            $('#addNotificationForm .hiddenInputs').html(hiddenInputs);
        }
    }
</script>

@endsection
