@extends('layouts.app')
@section('title', trans('cruds.shift.title_singular'))

@section('customCss')
<link href="{{asset('plugins/jquery-ui/jquery.ui.min.css')}}" rel="stylesheet" />
<link href="{{asset('plugins/timepicker/jquery.timepicker.css')}}" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<style>
    .daterangepicker{
        z-index: 9999 !important;
    }
</style>
@endsection

@section('main-content')
    <div class="animate__animated animate__fadeInUp">
        <div class="msg-content white-bg radius-50 space-30 d-flex align-items-center">
            <h2 class="mb-md-0">@lang('cruds.shift.title')</h2>
            @can('shift_create')
                <a href="javascript:void(0)" class="add_btn dash-btn green-bg w-115" id="addShiftBtn" >+ @lang('global.add') @lang('global.new')</a><div class="d-sm-none w-100"></div>
            @endcan
            @can('shift_delete')
                <button class="del_btn dash-btn red-bg w-115 me-md-1" id="deleteAllShift">@lang('global.delete')</button>
            @endcan
        </div>
        <div class="c-admin position-relative">
            <div class="table-responsive">
                {{$dataTable->table(['class' => 'table common-table short-table nowrap', 'style' => 'width:100%;'])}}
            </div>
        </div>
    </div>

    <!-- Add Rating Modal -->
	<div class="modal fade common-modal modal-size-l" id="addRatingsModal" tabindex="-1" aria-labelledby="addRatingsLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered mw-820">
			<div class="modal-content">
				<div class="modal-header justify-content-center lyellow-bg">
					<h5 class="modal-title text-center" id="addRatingsLabel">+ @lang('global.add') @lang('cruds.shift.fields.rating')</h5>
					<button type="button" class="btn-close cancelButton" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form class="msg-form" id="RatingForm" data-action="">
                        @csrf
						<div class="form-label select-label">
							<label>@lang('cruds.shift.fields.rating'):</label>
                            <select name="rating" class="select2" >
                                @foreach (config('constant.ratings') as $key => $val)
                                    <option value="{{$key}}" >{{ $val}}</option>
                                @endforeach
                            </select>
                            <div class="shift_name_error error_select"></div>
						</div>
						<div class="form-label justify-content-center">
							<input type="submit" value="@lang('global.submit')" class="cbtn submitBtn">
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

    {{-- Show Modal --}}
    {{-- @include('admin.shift.clock-in-out') --}}
@endsection

@section('customJS')

@parent
{!! $dataTable->scripts() !!}

<script src="{{asset('plugins/jquery-ui/jquery.ui.min.js')}}"></script>
<script src="{{asset('plugins/timepicker/jquery.timepicker.js')}}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script defer src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap" async></script>
<script>
        const dateFormat = "DD-MM-YYYY";
        var step = parseInt("{{config('constant.timepicker_step')}}");

    function initMap() {
        document.querySelectorAll('.map-container').forEach(function(element) {
            var lat = parseFloat(element.dataset.lat);
            var lng = parseFloat(element.dataset.lng);
            var map = new google.maps.Map(element, {
                center: { 
                    lat: lat, 
                    lng: lng 
                },
                zoom: 8
            });
            var marker = new google.maps.Marker({
                position: { lat: lat, lng: lng },
                map: map
            });
        });
    }

    
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

        var clonedRow = $('.template-row').first().clone().removeClass('template-row').removeClass('d-none');
        addNewRow(clonedRow);

    });

    $('.cancelButton').click(function() {
        $('#shift-table').DataTable().ajax.reload(null, false);
        $('#RatingForm')[0].reset();
    });

    @can('shift_access')
        $(document).on('click', '.clockIn', function(e){
            e.preventDefault();
            // $('.loader-div').show();
            var shift = $(this).data("shift_id");
            var type = 'ClockIn';
            $.ajax({
                type: 'get',
                url: "{{ route('shifts.clockInAndClockOut') }}",
                data: { 
                    shift_id: shift,
                    type:   type,
                },
                dataType: 'json',
                success: function (response) {
                    if(response.success) {
                        $('.popup_render_div').html(response.htmlView);
                        $('#clockIn').modal('show');

                        setTimeout(function() {
                            initMap();
                        }, 100);
                    }
                },
                error: function (response) {
                    if(response.responseJSON.error_type == 'something_error'){
                        toasterAlert('error',response.responseJSON.error);
                    }
                },
            })
        });

        $(document).on('click', '.clockOut', function(e){
            e.preventDefault();
            // $('.loader-div').show();
            var shift = $(this).data("shift_id");
            var type = 'ClockOut';
            $.ajax({
                type: 'get',
                url: "{{ route('shifts.clockInAndClockOut') }}",
                data: { 
                    shift_id: shift,
                    type:   type,
                },
                dataType: 'json',
                success: function (response) {
                    if(response.success) {
                        $('.popup_render_div').html(response.htmlView);
                        $('#clockOut').modal('show');

                        setTimeout(function() {
                            initMap();
                        }, 100);
                    }
                },
                error: function (response) {
                    if(response.responseJSON.error_type == 'something_error'){
                        toasterAlert('error',response.responseJSON.error);
                    }
                },
            })
        });

        $(document).on('click', '.timeSheet', function(e){
            e.preventDefault();
            var shift = $(this).data("shift_id");
            var type = 'TimeSheet';
            $.ajax({
                type: 'get',
                url: "{{ route('shifts.clockInAndClockOut') }}",
                data: { 
                    shift_id: shift,
                    type:   type,
                },
                dataType: 'json',
                success: function (response) {
                    if(response.success) {
                        $('.popup_render_div').html(response.htmlView);
                        $('#timeSheet').modal('show');
                    }
                },
                error: function (response) {
                    if(response.responseJSON.error_type == 'something_error'){
                        toasterAlert('error',response.responseJSON.error);
                    }
                },
            })
        });
        
    @endcan
    @can('shift_create')
        // Add Shift Modal
        $(document).on('click', '#addShiftBtn', function(e){
            e.preventDefault();
            $('.loader-div').show();
            $.ajax({
                type: 'get',
                url: "{{ route('shifts.create') }}",
                dataType: 'json',
                success: function (response) {
                    if(response.success) {
                        $('.popup_render_div').html(response.htmlView);
                        $('#addShiftModal').modal('show');
                    }
                },
                error: function (response) {
                    if(response.responseJSON.error_type == 'something_error'){
                        toasterAlert('error',response.responseJSON.error);
                    } 
                },
                complete: function(){
                    $('.loader-div').hide();
                }
            })
        });

        // Submit Add Shift Form
        $(document).on('submit', '#addShiftForm', function (e) {
            e.preventDefault();
            $('.validation-error-block').remove();
            if (!validateLastRowFields()) {
                return; // If validation fails, do not proceed with adding a new row
            }
            if(checkTimeFormat()){
                return false;
            }

            $('.loader-div').show();
            $(".submitBtn").attr('disabled', true);
            // var formData = new FormData(this);           
            var formData = collectFormData(this);

            $.ajax({
                type: 'post',
                url: "{{route('shifts.store')}}",
                dataType: 'json',
                contentType: false,
                processData: false,
                data: formData,
                success: function (response) {                
                    $(".submitBtn").attr('disabled', false);     
                    if(response.success) {
                        $('#shift-table').DataTable().ajax.reload(null, false);
                        $('#addShiftModal').modal('hide');
                        $('.popup_render_div').html('');
                        toasterAlert('success',response.message);
                    }
                },
                error: function (response) {
                    $(".submitBtn").attr('disabled', false);
                    $('.loader-div').hide();
                    if(response.responseJSON.error_type == 'something_error'){
                        toasterAlert('error',response.responseJSON.error);
                    } else {                    
                        var errorLabelTitle = '';
                        $('.validation-error-block').remove(); // Clear all previous error messages

                        $.each(response.responseJSON.errors, function (key, items) {
                            var indexMatch = key.match(/shifts\.(\d+)\.(.*)/);
                            if (indexMatch) {
                                var index = indexMatch[1]; // Extract index of the shift
                                var field = indexMatch[2]; // Extract the field name (e.g., start_time)                                

                                var elmt = $(`#row_${index}`).find(`.${field}`);
                                if (elmt.length > 0 ) {                                  
                                    errorLabelTitle = `<span class="validation-error-block" style="color: red;">${items[0]}</span>`;            
                                    if (elmt.is('select')) {
                                        elmt.closest('.form-label').after(errorLabelTitle);
                                    } else {
                                        $(errorLabelTitle).insertAfter(elmt.closest('.form-label'));
                                    }
                                }
                            } else {
                                // Handle other general errors that are not related to shifts
                                var elmt = $('#' + key);
                                var errorLabelTitle = `<span class="validation-error-block" style="color: red;">${items[0]}</span>`;
                                $(errorLabelTitle).insertAfter(elmt.closest('.form-label'));
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

    @can('shift_edit')
        // Edit Shift Modal
        $(document).on("click",".editShiftBtn", function() {  
            $('.loader-div').show();
            var url = $(this).data('href');
            $.ajax({
                type: 'get',
                url: url,
                dataType: 'json',
                success: function (response) {
                    if(response.success) {
                        $('.popup_render_div').html(response.htmlView);
                        $('#editShiftModal').modal('show');
                        
                        setTimeout(() => {
                            setOnEditTime();
                        }, 500);
                    }
                },
                error: function (response) {
                    if(response.responseJSON.error_type == 'something_error'){
                        toasterAlert('error',response.responseJSON.error);
                    } 
                }, 
                complete: function(){
                    $('.loader-div').hide();
                }
            });
        });
        
        // Submit Edit Shift Form
        $(document).on('submit', '#editShiftForm', function (e) {
            e.preventDefault();
            $('.validation-error-block').remove();
            if(checkTimeFormatEdit()){
                return false;
            }
            $('.loader-div').show();
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
                        $('#shift-table').DataTable().ajax.reload(null, false);
                        $('#editShiftModal').modal('hide');
                        toasterAlert('success',response.message);
                    }
                },
                error: function (response) {
                    $('.loader-div').hide();
                    $(".submitBtn").attr('disabled', false);
                    if(response.responseJSON.error_type == 'something_error'){
                        toasterAlert('error',response.responseJSON.error);
                    } else {                    
                        var errorLabelTitle = '';
                        $.each(response.responseJSON.errors, function (key, item) {
                            errorLabelTitle = '<span class="validation-error-block">'+item+'</sapn>';
                            var elmt = $('#'+key);
                            if (elmt.is('select')) {
                                elmt.closest('.select-label').find('.error_select').html(errorLabelTitle);
                            } 
                            else{
                                $(errorLabelTitle).insertAfter("input[name='"+key+"']");
                            }        
                        });
                    }
                },
                complete: function(res){
                    $('.loader-div').hide();
                    $(".submitBtn").attr('disabled', false);
                }
            });
        });
    @endcan

    @can('shift_delete')
        $(document).on("click",".deleteShiftBtn", function() {
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
                                $('#shift-table').DataTable().ajax.reload(null, false);
                                toasterAlert('success',response.message);
                            }
                            else {
                                toasterAlert('error',response.error);
                            }
                        },
                        error: function(res){
                            toasterAlert('error',res.responseJSON.error);
                        },
                        complete: function(){
                            $('.loader-div').hide();
                        }
                    });
                }
            });
        });

        $(document).on('click', '#deleteAllShift', function(e){
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
                        url: "{{route('shifts.massDestroy')}}",
                        type: "POST",
                        data: { 
                            ids: selectedIds,
                            _token: "{{ csrf_token() }}",
                        },
                        dataType: 'json',
                        success: function (response) {
                            if(response.success) {
                                $('#shift-table').DataTable().ajax.reload(null, false);
                                toasterAlert('success',response.message);
                            }
                            else {
                                toasterAlert('error',response.error);
                            }
                        },
                        error: function(res){
                            toasterAlert('error',res.responseJSON.error);
                        },
                        complete: function(){
                            $('.loader-div').hide();
                        }
                    })
                }
            });
        })
    @endcan

    @if(auth()->user()->is_super_admin)
        $(document).on('change', '#sub_admin_id', function(e){
            e.preventDefault();
            var t = $(this);
            var id = t.val();

            $.ajax({
                url  : "{{route('shifts.get-sub-admin-details')}}",
                type : 'GET',
                data : { 
                    id: id
                },
                dataType : 'json',
                success: function(response){
                    if(response.success){
                        $('#client_detail_id').html(response.client_detail_html);
                        $('#occupation_id').html(response.occupation_html);
                        $('#location_id').html(response.location_html);
                        $('.assign_staff').html(response.staff_html);
                    }
                },
                error: function(res){
                    toasterAlert('error',res.responseJSON.error);
                }
            });
        });
    @endif

    $(document).on("click",".cancelShiftBtn", function() {
        var url = $(this).data('href');
        Swal.fire({
            title: "{{ trans('global.areYouSure') }}",
            text: "{{ trans('global.want_to_cancel_shift') }}",
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
                    url: url,
                    dataType: 'json',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function (response) {
                        if(response.success) {
                            $('#shift-table').DataTable().ajax.reload(null, false);
                            toasterAlert('success',response.message);
                        }
                        else {
                            toasterAlert('error',response.error);
                        }
                    },
                    error: function(res){
                        toasterAlert('error',res.responseJSON.error);
                    },
                    complete: function(){
                        $('.loader-div').hide();
                    }
                });
            }
        });
    });

    $(document).on('click', '.ratingShiftBtn', function(e){
        e.preventDefault();

        var formActionUrl = $(this).data('href');
        var rating = $(this).data('rating');
        if(rating){
            $('select[name="rating"]').val(rating).trigger('change');
        }

        $('#RatingForm').data('action', formActionUrl);
        $('#RatingForm').attr('data-action', formActionUrl);
        $('#addRatingsModal').modal('show');
    });

    $(document).on('submit', '#RatingForm', function (e) {
        e.preventDefault();
        $('.loader-div').show();
        $('.validation-error-block').remove();
        $(".submitBtn").attr('disabled', true);

        var url = $(this).data('action');
        var formData = new FormData(this);
        $.ajax({
            type: 'post',
            url: url,
            dataType: 'json',
            contentType: false,
            processData: false,
            data: formData,
            success: function (response) {                
                if(response.success) {
                    $('#shift-table').DataTable().ajax.reload(null, false);
                    $('#addRatingsModal').modal('hide');
                    $('#RatingForm')[0].reset();
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
                        } else if (key.indexOf('shift_name') !== -1) {
                            $(".shift_name_error").html(errorLabelTitle);
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
</script>

@include('admin.shift._script');

@endsection
