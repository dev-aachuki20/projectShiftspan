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
            if(checkTimeFormat()){
                return false;
            }

            if (!validateLastRowFields()) {
                return; // If validation fails, do not proceed with adding a new row
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
                    if(response.success) {
                        $('#shift-table').DataTable().ajax.reload(null, false);
                        $('#addShiftModal').modal('hide');
                        $('.popup_render_div').html('');
                        toasterAlert('success',response.message);
                    }
                },
                error: function (response) {
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
                                var elmt = $(`.clone_row[data-row-index='${index}']`).find(`[name='${field}']`);

                                if (elmt.length === 0) {
                                    console.error(`Element not found for ${field} in shifts[${index}]`);
                                } else {                                    
                                    var errorLabelTitle = `<span class="validation-error-block" style="color: red;">${items[0]}</span>`;
                                    $(elmt).parent().after(errorLabelTitle);
                                }
                            } else {
                                // Handle other general errors that are not related to shifts
                                var elmt = $('#' + key);
                                var errorLabelTitle = `<span class="validation-error-block" style="color: red;">${items[0]}</span>`;
                                if (elmt.is('select')) {
                                    elmt.closest('.select-label').find('.error_select').html(errorLabelTitle);
                                } else {
                                    $(errorLabelTitle).insertAfter("input[name='" + key + "']");
                                }
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
            if(checkTimeFormat()){
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

    $(document).on('change', '#start_time', function(e){
        // e.preventDefault();
        var start_time = $(this).val();
        var startDate = $( "#start_date" ).datepicker( "getDate" );  
        var endDate = $( "#end_date" ).datepicker( "getDate" );  

        var parts = start_time.split(':');
        var hours = parts[0];
        var minutes = parts[1];
        var nextMinute = parseInt(minutes) + step;

        $("#end_time").val('');

        // $("#end_time").timepicker('option', 'minTime', hours + ':' + nextMinute);
        if(startDate && endDate && startDate < endDate){
            $("#end_time").timepicker('option', 'minTime', null);
        } else {
            $("#end_time").timepicker('option', 'minTime', hours+':'+nextMinute);
        }
    });

    function setOnEditTime(){
        // set start date
        var start_date = $('#start_date').val();
        $("#end_date").datepicker('option', 'minDate', start_date);

        var start_time = $('#start_time').val();

        var startDate = $( "#start_date" ).datepicker( "getDate" );  
        var endDate = $( "#end_date" ).datepicker( "getDate" );

        var currentDate = new Date();

        // Set min time of start time
        if (startDate.toDateString() === currentDate.toDateString()) {
            var currentHour = currentDate.getHours();
            var currentMinute = currentDate.getMinutes();
            var nextDivisibleMinute = Math.ceil(currentMinute / step) * step;

            $("#start_time").timepicker('option', 'minTime', currentHour + ':' + nextDivisibleMinute);
        } else {
            $("#start_time").timepicker('option', 'minTime', null);
        }

        var parts = start_time.split(':');
        var hours = parts[0];
        var minutes = parts[1];
        var nextMinute = parseInt(minutes) + step;

        $("#end_time").timepicker('option', 'minTime', hours + ':' + nextMinute);

    }
    
    function checkTimeFormatOld(){
        var errorLabelTitle = '';
        var startTime = $('.start_time').val();
        var endTime = $('.end_time').val();

        var errorFlg = false;

        var startTimePart = startTime.split(':'); 
        var endTimePart = endTime.split(':'); 

        var startTimePartOneStr = startTimePart[0].toString();
        var startTimePartTwoStr = startTimePart[1].toString();

        var endTimePartOneStr = endTimePart[0].toString();
        var endTimePartTwoStr = endTimePart[1].toString();

        var checkStartError = (startTimePartOneStr.length < 2 || startTimePartOneStr.length > 2) || (startTimePartTwoStr.length < 2 || startTimePartTwoStr.length > 2);
        var checkEndError = (endTimePartOneStr.length < 2 || endTimePartOneStr.length > 2) || (endTimePartTwoStr.length < 2 || endTimePartTwoStr.length > 2);

        if(checkStartError){
            errorLabelTitle = '<span class="validation-error-block">The start time must be in the format HH:MM.</sapn>';
            $(errorLabelTitle).insertAfter(".start_time");
            errorFlg = true;
        }

        if(checkEndError){
            errorLabelTitle = '<span class="validation-error-block">The end time must be in the format HH:MM.</sapn>';
            $(errorLabelTitle).insertAfter(".end_time");
            errorFlg = true;
        }
        if(!errorFlg){
            if(startTimePart[1]%step != 0){
                errorLabelTitle = '<span class="validation-error-block">The start time must be selected from list</sapn>';
                $(errorLabelTitle).insertAfter(".start_time");
                errorFlg = true;
            }

            if(endTimePart[1]%step != 0){
                errorLabelTitle = '<span class="validation-error-block">The end time must be selected from list</sapn>';
                $(errorLabelTitle).insertAfter(".end_time");
                errorFlg = true;
            }
        }
        return errorFlg;
    }

    function checkTimeFormat() {
        var errorFlg = false; // Flag to track if any error occurs
        var errorHtml = ''; // To accumulate error messages

        // Loop through each row with class 'clone_row'
        $('#clone-showing-data .clone_row').each(function() {
            var row = $(this); // Current row context
            // Get start_time and end_time values
            var startTime = row.find('.start_time').val();
            var endTime = row.find('.end_time').val();
            // Split time into parts
            var startTimePart = startTime.split(':');
            var endTimePart = endTime.split(':');
            // Validate start_time format
            if (startTimePart.length !== 2 || startTimePart[0].length !== 2 || startTimePart[1].length !== 2) {
                errorHtml += '<span class="validation-error-block">The start time must be in the format HH:MM.</span>';
                errorFlg = true;
            } else if (startTimePart[1] % step !== 0) {
                errorHtml += '<span class="validation-error-block">The start time must be selected from the list.</span>';
                errorFlg = true;
            }
            // Validate end_time format
            if (endTimePart.length !== 2 || endTimePart[0].length !== 2 || endTimePart[1].length !== 2) {
                errorHtml += '<span class="validation-error-block">The end time must be in the format HH:MM.</span>';
                errorFlg = true;
            } else if (endTimePart[1] % step !== 0) {
                errorHtml += '<span class="validation-error-block">The end time must be selected from the list.</span>';
                errorFlg = true;
            }
        });

        // If there are errors, display them after the last clone_row
        if (errorFlg) {
            // Clear existing error messages
            $('.validation-error-block').remove(); // Remove previous error messages
            $(errorHtml).insertAfter($('.clone_row:last')); // Insert accumulated error messages
        }

        return errorFlg; // Return whether there were any errors
    }

</script>

<script>   


    $(document).on('click', '.remove-option', function() {
        $(this).closest('.clone_row').remove();
    });    

    $(document).on('click', '.addicon', function() {
        if (!validateLastRowFields()) {
            return; // If validation fails, do not proceed with adding a new row
        }
        var clonedRow = $('.template-row').first().clone().removeClass('template-row').removeClass('d-none');
        clonedRow.find('.AddOptionBtn2').html('<a href="javascript:void(0)" class="remove-option"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M1.07996 10.0798H16.9199C17.5165 10.0798 18 9.59626 18 8.99954C18 8.40296 17.5166 7.91943 16.9199 7.91943H1.07996C0.483527 7.91958 0 8.40311 0 8.99969C0 9.59626 0.483527 10.0798 1.07996 10.0798Z" fill="white"/></svg></a>');
        addNewRow(clonedRow);    
    });

    
    function addNewRow(clonedRow){
        var rowIndex = $('#clone-showing-data .clone_row').length; 
        clonedRow.attr('data-row-index', rowIndex); // Set the row index attribute     
        clonedRow.attr('id', 'row_' + rowIndex);
        clonedRow.find('input').val('');     
        var selectBox = clonedRow.find(".assign_staff");        
        var select2Container = selectBox.next('.select2-container');
        if (select2Container.length > 0) {
            select2Container.remove();
        }

        if (rowIndex !== 0) {
            // Remove any existing select2 container for the cloned row if it's not the first row
            var select2Container = selectBox.next('.select2-container');
            if (select2Container.length > 0) {
                select2Container.remove();
            }
        }

        // Reinitialize select2 for the cloned row only
        selectBox.select2({
            width: 'calc(100% - 180px)',
            dropdownParent: clonedRow.find('.select-label').first(),
            selectOnClose: false
        });
        
        $('#clone-showing-data').append(clonedRow);      

        clonedRow.find('.start_date').daterangepicker({
            startDate: moment(),
            locale: { format: dateFormat }, 
            singleDatePicker: true,
            autoUpdateInput: true,
            autoApply: true,
            minDate: moment(),
        }, function(start, end, label) {            
            // Set the minimum date for the end date as the selected start date
            clonedRow.find('.end_date').daterangepicker({
                locale: { format: dateFormat }, 
                singleDatePicker: true,
                autoUpdateInput: true,
                autoApply: true,
                minDate: start, // Use the selected start date as the minimum end date
            });

            // Clear start and end time fields
            clonedRow.find(".start_time").val('');
            clonedRow.find(".end_time").val('');
            // Set the min time for start_time picker if start date is today
            let selectedDate = start.toDate(); // Convert moment object to JavaScript Date
            let currentDate = new Date();
            if (selectedDate.toDateString() === currentDate.toDateString()) {
                var currentHour = currentDate.getHours();
                var currentMinute = currentDate.getMinutes();
                var nextDivisibleMinute = Math.ceil(currentMinute / step) * step;
                // Set min time for start time
                clonedRow.find(".start_time").timepicker('option', 'minTime', currentHour + ':' + nextDivisibleMinute);
            } else {
                // No min time for start time if the selected date is not today
                clonedRow.find(".start_time").timepicker('option', 'minTime', null);
            }
        });  
         
        clonedRow.find('.end_date').daterangepicker({
            locale: { format: dateFormat },
            singleDatePicker: true,
            autoUpdateInput: true,
            autoApply: true,
            minDate: moment() // Ensure the initial minimum is today
        });

        clonedRow.find( ".start_time" ).timepicker({ 
			show2400: true,
            step: step,
            timeFormat: "{{config('constant.js_date_format.time')}}",
            // disableTextInput: true,
            maxTime: '24:00'
		});

        clonedRow.find( ".end_time" ).timepicker({ 
			show2400: true,
            step: step,
            timeFormat: "{{config('constant.js_date_format.time')}}",
            // disableTextInput: true,
            maxTime: "{{config('constant.timepicker_max_time')}}"
		});        
    }

    function validateLastRowFields() {
        removError();
        let isValid = true;
        let errors = {};
        // Define the fields that need validation
        let fieldsToValidate = [
            { key: 'start_date', label: 'Start Date' },
            { key: 'end_date', label: 'End Date' },
            { key: 'start_time', label: 'Start Time' },
            { key: 'end_time', label: 'End Time' },
            // { key: 'assign_staff', label: 'Assign Staff' }
        ];
        // Get the last row for validation
        let lastRow = $('.clone_row').last();
        // Clear any previous error messages
        lastRow.find('.error.text-danger').remove();
        // Iterate over each field that needs validation for the last row
        fieldsToValidate.forEach(function(field) {
            let fieldValue = lastRow.find(`.${field.key}`).val();
            if (!fieldValue) {
                isValid = false;
                errors[field.key] = `${field.label} is required`; 
            }
        });

        if (!isValid) {
            for (let key in errors) {
                let errorHtml = '<div><span class="error text-danger">' + errors[key] + '</span></div>';
                lastRow.find(`.${key}`).parent().parent().append(errorHtml);
            }
        }
        return isValid;
    }

    function removError(){
        $(".error.text-danger").remove();
        $(".is-invalid").removeClass("is-invalid");
    }

    function collectFormData(formElement) {
        var formData = new FormData(formElement);
        var shifts = [];

        $('#clone-showing-data .clone_row').each(function() {
            var row = $(this);
            var shiftData = {
                start_date: row.find('.start_date').val(),
                end_date: row.find('.end_date').val(),
                start_time: row.find('.start_time').val(),
                end_time: row.find('.end_time').val(),
                assign_staff: row.find('.assign_staff').val()
            };
            shifts.push(shiftData);
        });

        // Append shifts as a plain array
        shifts.forEach(function(shift, index) {
            formData.append(`shifts[${index}][start_date]`, shift.start_date);
            formData.append(`shifts[${index}][end_date]`, shift.end_date);
            formData.append(`shifts[${index}][start_time]`, shift.start_time);
            formData.append(`shifts[${index}][end_time]`, shift.end_time);
            formData.append(`shifts[${index}][assign_staff]`, shift.assign_staff);
        });

        return formData;
    }

</script>

@endsection
