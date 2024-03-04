@extends('layouts.app')
@section('title', trans('cruds.message.title_singular'))

@section('customCss')
<link href="{{asset('plugins/jquery-ui/jquery.ui.min.css')}}" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('main-content')
    <div class="animate__animated animate__fadeInUp">
        <div class="msg-content white-bg radius-50 space-30 d-flex align-items-center">
            <h2 class="mb-md-0">@lang('cruds.message.fields.message_center')</h2>
            @can('message_create')
                <a href="javascript:void(0)" class="add_btn dash-btn green-bg" data-bs-toggle="modal" data-bs-target="#NnotificationSettings">+ @lang('cruds.message.fields.new_message') </a><div class="d-sm-none w-100"></div>
            @endcan

            @can('message_delete')
                <button class="del_btn dash-btn red-bg" id="deleteAllMessage">@lang('cruds.message.fields.delete_message')(s)</button>
            @endcan
        </div>
        <div class="c-admin position-relative">
            <div class="table-responsive">
                {{$dataTable->table(['class' => 'table common-table', 'style' => 'width:100%;'])}}
            </div>
        </div>
    </div>

    {{-- Notification model --}}
    @include('admin.staff.notification.create')
@endsection

@section('customJS')

@parent
{!! $dataTable->scripts() !!}
<script src="{{asset('plugins/jquery-ui/jquery.ui.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
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

    $(document).ready(function(){
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
    });

    @can('message_create')
        $(document).on('submit', '#addNotificationForm', function (e) {
            e.preventDefault();
            $('.validation-error-block').remove();
            $(".submitBtn").attr('disabled', true);

            var formData = new FormData(this);

            $.ajax({
                type: 'post',
                url: "{{route('messages.store')}}",
                dataType: 'json',
                contentType: false,
                processData: false,
                data: formData,
                success: function (response) {         
                    if(response.success) {
                        $('#addNotificationForm')[0].reset();
                        $('#NnotificationSettings').modal('hide');
                        
                        var selected = [];
                        selected.push($(this).closest(".select-option").find('span').text());
                        $('.selected-options').text(selected.length > 0 ? 'Select...' : 'Select...');

                        $('.popup_render_div').html('');
                        $('#message-centre-table').DataTable().ajax.reload(null, false);
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
    @endcan

    @can('message_delete')
        $(document).on('click', '#deleteAllMessage', function(e){
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
                        url: "{{route('messages.massDestroy')}}",
                        type: "POST",
                        data: { 
                            ids: selectedIds,
                            _token: "{{ csrf_token() }}",
                        },
                        dataType: 'json',
                        success: function (response) {
                            if(response.success) {
                                $('#message-centre-table').DataTable().ajax.reload(null, false);
                                setTimeout(() => {
                                    $('#dt_cb_all').prop('checked', false);
                                }, 500);
                                toasterAlert('success',response.message);
                            }
                            else {
                                toasterAlert('error',response.message);
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

    $('.btn-close').click(function() {
        $('.validation-error-block').remove();
    });
</script>

@endsection
