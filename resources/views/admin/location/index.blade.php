@extends('layouts.app')
@section('title', trans('cruds.location.title_singular'))
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@section('customCss')
@endsection

@section('main-content')

    <div class="animate__animated animate__fadeInUp">
        <div class="msg-content white-bg radius-50 space-30 d-flex align-items-center">
            <h2 class="mb-md-0">@lang('cruds.location.title_singular')</h2>
            @can('location_create')
                <a href="javascript:void(0)" class="add_btn dash-btn green-bg w-115" id="addLocationBtn" >+ @lang('global.add') @lang('global.new')</a><div class="d-sm-none w-100"></div>
            @endcan
            @can('location_delete')
                <button class="del_btn dash-btn red-bg w-115 me-md-1" id="deleteAllLocation">@lang('global.delete')</button>
            @endcan
        </div>
        <div class="c-admin position-relative">
            <div class="table-responsive">
                {{$dataTable->table(['class' => 'table common-table short-table nowrap', 'style' => 'width:100%;'])}}
            </div>
        </div>
    </div>

    {{-- add new location by sub-admin --}}
    <div class="modal fade common-modal modal-size-l" id="addNewLocationModal" tabindex="-1" aria-labelledby="addLocationLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-820">
            <div class="modal-content">
                <div class="modal-header justify-content-center green-bg">
                    <h5 class="modal-title text-center" id="addLocationLabel">+ @lang('global.add') @lang('global.new') @lang('cruds.location.title_singular')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" >
                    <form class="msg-form" id="addNewLocationForm" action="{{route('locations.store')}}">
                        @csrf
                        <div class="form-label">
                            <label>@lang('cruds.location.title_singular') @lang('cruds.location.fields.name'):</label>
                            <input type="text" name="name" value="">
                        </div>
                        <div class="form-label justify-content-center">
                            <button type="submit" class="cbtn submitBtn">
                                @lang('global.add') @lang('global.new') @lang('cruds.location.title_singular')
                            </button>
                        </div>
                    </form>
                </div>
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
            width: 'calc(100% - 180px)',
            dropdownParent: $('.select-label'),
            selectOnClose: false
        }).on('select2:close', function() {
            var el = $(this);
            if(el.val()==="new") {
                el.val('').change();
                $('#addNewLocationModal').modal('show');
            }
        });
    });
    @can('location_create')
        // Add Location Modal
        $(document).on('click', '#addLocationBtn', function(e){
            e.preventDefault();
            // $('#pageloader').css('display', 'block');
            $.ajax({
                type: 'get',
                url: "{{ route('locations.create') }}",
                dataType: 'json',
                success: function (response) {
                    // $('#pageloader').css('display', 'none');
                    if(response.success) {
                        $('.popup_render_div').html(response.htmlView);
                        $('#addLocationModal').modal('show');
                    }
                },
                error: function (response) {
                    if(response.responseJSON.error_type == 'something_error'){
                        toasterAlert('error',response.responseJSON.error);
                    } 
                }
            })
        });

        // Submit Add Location Form
        $(document).on('submit', '#addLocationForm', function (e) {
            e.preventDefault();
            $('.validation-error-block').remove();
            $(".submitBtn").attr('disabled', true);

            var formData = new FormData(this);

            $.ajax({
                type: 'post',
                url: "{{route('locations.store')}}",
                dataType: 'json',
                contentType: false,
                processData: false,
                data: formData,
                success: function (response) {                
                    if(response.success) {
                        $('#location-table').DataTable().ajax.reload(null, false);
                        $('#addLocationModal').modal('hide');
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
                            } else if (key.indexOf('location_name') !== -1) {
                                $(".location_name_error").html(errorLabelTitle);
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

        $(document).on('submit', '#addNewLocationForm', function (e) {
            e.preventDefault();
            $('.validation-error-block').remove();
            $(".submitBtn").attr('disabled', true);

            var formData = new FormData(this);

            formData.append('save_type', 'new_location')

            $.ajax({
                type: 'post',
                url: "{{route('locations.store')}}",
                dataType: 'json',
                contentType: false,
                processData: false,
                data: formData,
                success: function (response) {                
                    if(response.success) {
                        $('#location-table').DataTable().ajax.reload(null, false);
                        $('#addLocationModal').modal('hide');
                        $('#addNewLocationModal').modal('hide');

                        $('#addNewLocationForm')[0].reset();
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

    @can('location_edit')
        // Edit Location Modal
        $(document).on("click",".editLocationBtn", function() {
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
                        $('#editLocationModal').modal('show');
                    }
                },
                error: function (response) {
                    if(response.responseJSON.error_type == 'something_error'){
                        toasterAlert('error',response.responseJSON.error);
                    } 
                }
            });
        });
        
        // Submit Edit Location Form
        $(document).on('submit', '#editLocationForm', function (e) {
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
                        $('#location-table').DataTable().ajax.reload(null, false);
                        $('#editLocationModal').modal('hide');
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

    @can('location_delete')
        $(document).on("click",".deleteLocationBtn", function() {
            var url = $(this).data('href');
            Swal.fire({
            title: "{{ trans('global.areYouSure') }}",
            text: "{{ trans('global.onceClickedRecordDeleted') }}",
            icon: "warning",
            showDenyButton: true,  
            //   showCancelButton: true,  
            confirmButtonText: "Yes, I am sure",  
            denyButtonText: "No, cancel it!",
            })
            .then(function(result) {
                if (result.isConfirmed) {  
                    $.ajax({
                        type: 'DELETE',
                        url: url,
                        dataType: 'json',
                        data: { _token: "{{ csrf_token() }}" },
                        success: function (response) {
                            if(response.success) {
                                $('#location-table').DataTable().ajax.reload(null, false);
                                toasterAlert('success',response.message);
                            }
                            else {
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

        $(document).on('click', '#deleteAllLocation', function(e){
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
            confirmButtonText: "Yes, I am sure",  
            denyButtonText: "No, cancel it!",
            })
            .then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{route('locations.massDestroy')}}",
                        type: "POST",
                        data: { 
                            ids: selectedIds,
                            _token: "{{ csrf_token() }}",
                        },
                        dataType: 'json',
                        success: function (response) {
                            if(response.success) {
                                $('#location-table').DataTable().ajax.reload(null, false);
                                toasterAlert('success',response.message);
                            }
                            else {
                                toasterAlert('error',response.error);
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

    
</script>

@endsection
