@extends('layouts.app')
@section('title', trans('quickadmin.occupation.title'))
@section('customCss')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('main-content')

    <div class="animate__animated animate__fadeInUp">
        <div class="msg-content white-bg radius-50 space-30 d-flex align-items-center">
            <h2 class="mb-md-0">@lang('cruds.occupation.title_singular')</h2>
            @can('occupation_create')
                <a href="javascript:void(0)" class="add_btn dash-btn green-bg w-115" id="addOccupationBtn" >+ @lang('global.add') @lang('global.new')</a><div class="d-sm-none w-100"></div>
            @endcan
            @can('occupation_delete')
                <button class="del_btn dash-btn red-bg w-115 me-md-1" id="deleteAllOccupation">@lang('global.delete')</button>
            @endcan
        </div>
        <div class="c-admin position-relative">
            <div class="table-responsive">
                {{$dataTable->table(['class' => 'table common-table short-table nowrap', 'style' => 'width:100%;'])}}
            </div>
        </div>
    </div>

    {{-- add new occupation by sub-admin --}}
    <div class="modal fade common-modal modal-size-l" id="addNewOccupationModal" tabindex="-1" aria-labelledby="addOccupationLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-820">
            <div class="modal-content">
                <div class="modal-header justify-content-center green-bg">
                    <h5 class="modal-title text-center" id="addOccupationLabel">+ @lang('global.add') @lang('global.new') @lang('cruds.occupation.title_singular')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" >
                    <form class="msg-form" id="addNewOccupationForm" >
                        @csrf
                        <div class="form-label">
                            <label>@lang('cruds.occupation.title_singular') @lang('cruds.occupation.fields.name'):</label>
                            <input type="text" name="name" value="">
                        </div>
                        <div class="form-label justify-content-center">
                            <button type="submit" class="cbtn submitBtn">
                                @lang('global.add') @lang('global.new') @lang('cruds.occupation.title_singular')
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
                $('#addNewOccupationModal').modal('show');
            }
        });
    });

    function Checkselect2() {
        $('#occupation_name').on('select2:open', function (e) {
            let a = $(this).data('select2');
            if (!$('.select2-group_add').length) {
                let buttonText = "+ @lang('global.add') @lang('cruds.occupation.title_singular')";
                /* let buttonHtml = '<li class="select2-results__option">';
                    buttonHtml += '<button id="" class="newAddLocation" data-bs-toggle="modal" data-bs-target="#addNewOccupationModal">' + buttonText + '</button></li>'; */

                let buttonHtml = '<button id="" class="select2-group_add newAddLocation" data-bs-toggle="modal" data-bs-target="#addNewOccupationModal">' + buttonText + '</button>';
                a.$results.parents('.select2-results').prepend(buttonHtml);
                
                $('.newAddLocation').click(function(event){
                    event.preventDefault();
                });
            }
        });

        $('#location_name').on('select2:close', function (e) {
            $('.select2-group_add').remove();
        });
    }

    @can('occupation_create')
        $(document).on('click', '#addOccupationBtn', function(e){
            e.preventDefault();
            $('.loader-div').show();

            $.ajax({
                type: 'get',
                url: "{{ route('occupations.create') }}",
                dataType: 'json',
                success: function (response) {
                    // $('#pageloader').css('display', 'none');
                    if(response.success) {
                        $('.popup_render_div').html(response.htmlView);
                        $('#addOccupationModal').modal('show');
                        Checkselect2();
                        $('.loader-div').hide();
                    }
                },
                error: function (response) {
                    if(response.responseJSON.error_type == 'something_error'){
                        toasterAlert('error',response.responseJSON.error);
                        $('.loader-div').hide();
                    } 
                }
            })
        });

        // Submit Add Occupation Form
        $(document).on('submit', '#addOccupationForm', function (e) {
            e.preventDefault();
            $('.loader-div').show();

            $('.validation-error-block').remove();
            $(".submitBtn").attr('disabled', true);

            var formData = new FormData(this);

            $.ajax({
                type: 'post',
                url: "{{route('occupations.store')}}",
                dataType: 'json',
                contentType: false,
                processData: false,
                data: formData,
                success: function (response) {                
                    if(response.success) {
                        $('#addOccupationModal').modal('hide');
                        toasterAlert('success',response.message);
                        $('#occupation-table').DataTable().ajax.reload(null, false);
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
                    $('.loader-div').hide();
                }
            });                    
        });

        $(document).on('submit', '#addNewOccupationForm', function (e) {
            e.preventDefault();
            $('.loader-div').show();

            $('.validation-error-block').remove();
            $(".submitBtn").attr('disabled', true);

            var formData = new FormData(this);

            formData.append('save_type', 'new_occupation')

            $.ajax({
                type: 'post',
                url: "{{route('occupations.store')}}",
                dataType: 'json',
                contentType: false,
                processData: false,
                data: formData,
                success: function (response) {                
                    if(response.success) {
                        $('#occupation-table').DataTable().ajax.reload(null, false);
                        $('#addOccupationModal').modal('hide');
                        $('#addNewOccupationModal').modal('hide');

                        $('#addNewOccupationForm')[0].reset();
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
                    $('.loader-div').hide();
                }
            });                    
        });
    @endcan

    @can('occupation_edit')
        /* Edit Occupation Modal */
        $(document).on("click",".editOccupationBtn", function (e) {
            e.preventDefault();
            $('.loader-div').show();
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
                        $('#editOccupationModal').modal('show');
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
        
        // Submit Edit Occupation Form
        $(document).on('submit', '#editOccupationForm', function (e) {
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
                        $('#editOccupationModal').modal('hide');
                        toasterAlert('success',response.message);
                        $('#occupation-table').DataTable().ajax.reload(null, false);
                    }
                },
                error: function (response) {
                    $(".submitBtn").attr('disabled', false);
                    if(response.responseJSON.error_type == 'something_error'){
                        toasterAlert('error',response.responseJSON.error_type.error);
                    } else {                    
                        var errorLabelTitle = '';
                        $.each(response.responseJSON.errors, function (key, item) {
                            errorLabelTitle = '<span class="validation-error-block">'+item[0]+'</sapn>';
                            console.log("input[name='"+key+"']");
                            $(errorLabelTitle).insertAfter("input[name='"+key+"']");
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

    @can('occupation_delete')
        $(document).on("click",".deleteOccupationBtn", function() {
            var url = $(this).data('href');
            Swal.fire({
                title: "{{ trans('global.areYouSure') }}",
                text: "{{ trans('global.onceClickedRecordDeleted') }}",
                icon: "warning",
                showDenyButton: true,  
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
                                $('#occupation-table').DataTable().ajax.reload(null, false);
                                toasterAlert('success',response.message);
                                $('.loader-div').hide();
                            }
                            else {
                                toasterAlert('error',response.message);
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

        $(document).on('click', '#deleteAllOccupation', function(e){
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
                        url: "{{route('occupations.massDestroy')}}",
                        type: "POST",
                        data: { 
                            ids: selectedIds,
                            _token: "{{ csrf_token() }}",
                        },
                        dataType: 'json',
                        success: function (response) {
                            if(response.success) {
                                $('#occupation-table').DataTable().ajax.reload(null, false);
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
                        }
                    })
                }
            });
        })
    @endcan
</script>

@endsection