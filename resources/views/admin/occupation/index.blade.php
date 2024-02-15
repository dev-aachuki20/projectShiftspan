@extends('layouts.app')
@section('title')@lang('quickadmin.occupation.title')@endsection
@section('customCss')
<meta name="csrf-token" content="{{ csrf_token() }}" >
@endsection

@section('main-content')


<div class="right-content">
    <div class="animate__animated animate__fadeInUp">
        <div class="msg-content white-bg radius-50 space-30 d-flex align-items-center">
            <h2 class="mb-md-0">@lang('cruds.occupation.title_singular')</h2>
            <a href="javascript:void(0)" class="add_btn dash-btn green-bg w-115" id="addOccupationBtn" >+ @lang('global.add') @lang('global.new')</a><div class="d-sm-none w-100"></div>
            <button class="del_btn dash-btn red-bg w-115 me-md-1" id="deleteAllOccupation">@lang('global.delete')</button>
        </div>
        <div class="c-admin position-relative">
            <div class="table-responsive">
                {{$dataTable->table(['class' => 'table common-table short-table nowrap', 'style' => 'width:100%;'])}}
            </div>
        </div>
    </div>
</div>


@endsection
@section('customJS')

@parent
{!! $dataTable->scripts() !!}

<script>
    @can('occupation_create')
        $(document).on('click', '#addOccupationBtn', function(e){
            e.preventDefault();
            // $('#pageloader').css('display', 'block');
            $.ajax({
                type: 'get',
                url: "{{ route('occupations.create') }}",
                dataType: 'json',
                success: function (response) {
                    // $('#pageloader').css('display', 'none');
                    if(response.success) {
                        $('.popup_render_div').html(response.htmlView);
                        $('#addOccupationModal').modal('show');
                    }
                }
            })
        });

        // Submit Add Occupation Form
        $(document).on('submit', '#addOccupationForm', function (e) {
            e.preventDefault();
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
                        fireSuccessSwal('Success',response.message);
                        $('#occupation-table').DataTable().ajax.reload(null, false);
                    }
                },
                error: function (response) {
                    if(response.error_type == 'something_error'){
                        fireErrorSwal('Error',response.message);
                    } else {                    
                        var errorLabelTitle = '';
                        $.each(response.responseJSON.errors, function (key, item) {
                            errorLabelTitle = '<span class="validation-error-block">'+item+'</sapn>';
                            if(key == 'resume' || key == 'gender'){
                                $('#'+key).html(errorLabelTitle);
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
    @endcan

    @can('occupation_edit')
        /* Edit Occupation Modal */
        $(document).on("click",".editOccupationBtn", function() {
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
                        $('#editOccupationModal').modal('show');
                    }
                }
            });
        });
        
        // Submit Edit Occupation Form
        $(document).on('submit', '#editOccupationForm', function (e) {
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
                        $('#editOccupationModal').modal('hide');
                        fireSuccessSwal('Success',response.message);
                        $('#occupation-table').DataTable().ajax.reload(null, false);
                    }
                },
                error: function (response) {
                    $(".submitBtn").attr('disabled', false);
                    if(response.error_type == 'something_error'){
                        fireErrorSwal('Error',response.message);
                    } else {                    
                        var errorLabelTitle = '';
                        $.each(response.responseJSON.errors, function (key, item) {
                            errorLabelTitle = '<span class="validation-error-block">'+item+'</sapn>';
                            if(key == 'resume' || key == 'gender'){
                                $('#'+key).html(errorLabelTitle);
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
    @endcan

    @can('occupation_delete')
        $(document).on("click",".deleteOccupationBtn", function() {
            var url = $(this).data('href');
            Swal.fire({
            title: "{{ trans('global.areYouSure') }}",
            text: "{{ trans('global.onceClickedRecordDeleted') }}",
            icon: "warning",
            showDenyButton: true,  
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
                                fireSuccessSwal('Success',response.message);
                                $('#occupation-table').DataTable().ajax.reload(null, false);
                            }
                            else {
                                fireErrorSwal('Error',response.message);
                            }
                        },
                        error: function(res){
                            fireErrorSwal('Error',res.message);
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
            confirmButtonText: "Yes, I am sure",  
            denyButtonText: "No, cancel it!",
            })
            .then(function(result) {
                if (result.isConfirmed) {       
                    console.log(selectedIds);   
                    $.ajax({
                        url: "{{route('getMultipleOccupationToDelete')}}",
                        type: "POST",
                        data: { 
                            ids: selectedIds,
                            _token: "{{ csrf_token() }}",
                        },
                        dataType: 'json',
                        success: function (response) {
                            if(response.success) {
                                fireSuccessSwal('Success',response.message);
                                $('#occupation-table').DataTable().ajax.reload(null, false);
                            }
                            else {
                                fireErrorSwal('Error',response.message);
                            }
                        },
                        error: function(res){
                            fireErrorSwal('Error',res.message);
                        }
                    })
                }
            });
        })
    @endcan
</script>

@endsection