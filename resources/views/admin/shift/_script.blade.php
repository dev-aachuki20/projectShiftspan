<script> 
    $(document).on('change', '.start_time', function(e){
        let _this = $(this);
        let closestRow = _this.closest('.clone_row');

        let start_time = _this.val();
        let startDate = closestRow.find( ".start_date" ).datepicker( "getDate" );  
        let endDate = closestRow.find( ".end_date" ).datepicker( "getDate" );
        
        closestRow.find( ".end_time").val('');
        
        if(startDate && endDate && startDate < endDate){
            closestRow.find( ".end_time").timepicker('option', 'minTime', null);
        } else {
            let setEndTime = getCorrectTime(start_time);
            closestRow.find( ".end_time").timepicker('option', 'minTime', setEndTime.hour+':'+setEndTime.minute);
        }
    });
    
    function setOnEditTime(){
        $( "#start_date" ).datepicker({ 
			dateFormat: "{{config('constant.js_date_format.date')}}", 
			minDate: 0,
			firstDay: 1,
            onSelect: function(date) {
                var parts = date.split('-'); 
                var selectedDate = new Date(parts[2], parts[1] - 1, parts[0]); 
                
                var currentDate = new Date(); 

                $("#end_date").val('');                

                //  set min date of end date
                $("#end_date").datepicker('option', 'minDate', date);

                $("#start_time").val('');
                $("#end_time").val('');
                
                // set min time of start time
                if (selectedDate.toDateString() === currentDate.toDateString()) {
                    let setEndTime = getCorrectTime(currentDate, 'full_date');
                    $("#start_time").timepicker('option', 'minTime', setEndTime.hour + ':' + setEndTime.minute);
                } else {
                    $("#start_time").timepicker('option', 'minTime', null);
                }
            }
		});

        $( "#end_date" ).datepicker({ 
			dateFormat: "{{config('constant.js_date_format.date')}}", 
			minDate: 0,
			firstDay: 1,
            onSelect: function(date) {
                var parts = date.split('-'); 
                var endDate = new Date(parts[2], parts[1] - 1, parts[0]); 
                
                var startDate = $( "#start_date" ).datepicker( "getDate" );

                $("#start_time").val('');
                $("#end_time").val('');
            }
		});

        $( "#start_time" ).timepicker({ 
			show2400: true,
            step: step,
            timeFormat: "{{config('constant.js_date_format.time')}}",
            // disableTextInput: true,
            maxTime: '24:00'
		});

        $( "#end_time" ).timepicker({ 
			show2400: true,
            step: step,
            timeFormat: "{{config('constant.js_date_format.time')}}",
            // disableTextInput: true,
            maxTime: "{{config('constant.timepicker_max_time')}}"
		});
        // set start date
        var start_date = $('#start_date').val();
        $("#end_date").datepicker('option', 'minDate', start_date);

        var start_time = $('#start_time').val();

        var startDate = $( "#start_date" ).datepicker( "getDate" );  
        var endDate = $( "#end_date" ).datepicker( "getDate" );

        var currentDate = new Date();

        // Set min time of start time
        if (startDate.toDateString() === currentDate.toDateString()) {
            let setEndTime = getCorrectTime(currentDate, 'full_date');
            $("#start_time").timepicker('option', 'minTime', setEndTime.hour + ':' + setEndTime.minute);
        } else {
            $("#start_time").timepicker('option', 'minTime', null);
        }

        let setEndTime1 = getCorrectTime(start_time);  

        $("#end_time").timepicker('option', 'minTime', setEndTime1.hour + ':' + setEndTime1.minute);
    }
    
    function checkTimeFormatEdit(){
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
            $(errorLabelTitle).insertAfter($(".start_time").closest('.form-label'));
            errorFlg = true;
        }

        if(checkEndError){
            errorLabelTitle = '<span class="validation-error-block">The end time must be in the format HH:MM.</sapn>';
            $(errorLabelTitle).insertAfter($(".end_time").closest('.form-label'));
            errorFlg = true;
        }
        if(!errorFlg){
            if(startTimePart[1]%step != 0){
                errorLabelTitle = '<span class="validation-error-block">The start time must be selected from list</sapn>';
                $(errorLabelTitle).insertAfter($(".start_time").closest('.form-label'));
                errorFlg = true;
            }

            if(endTimePart[1]%step != 0){
                errorLabelTitle = '<span class="validation-error-block">The end time must be selected from list</sapn>';
                $(errorLabelTitle).insertAfter($(".end_time").closest('.form-label'));
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
                errorHtml = '<span class="validation-error-block">The start time must be in the format HH:MM.</span>';
                errorFlg = true;
            } else if (startTimePart[1] % step !== 0) {
                errorHtml = '<span class="validation-error-block">The start time must be selected from the list.</span>';
                errorFlg = true;
            }
            // Validate end_time format
            if (endTimePart.length !== 2 || endTimePart[0].length !== 2 || endTimePart[1].length !== 2) {
                errorHtml = '<span class="validation-error-block">The end time must be in the format HH:MM.</span>';
                errorFlg = true;
            } else if (endTimePart[1] % step !== 0) {
                errorHtml = '<span class="validation-error-block">The end time must be selected from the list.</span>';
                errorFlg = true;
            }


        });

        // If there are errors, display them after the last clone_row
        if (errorFlg) {
            // Clear existing error messages
            $('.validation-error-block').remove(); // Remove previous error messages
            $(errorHtml).insertAfter($('#clone-showing-data')); // Insert accumulated error messages
        }

        return errorFlg; // Return whether there were any errors
    }

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
        clonedRow.attr('data-row_index', rowIndex); // Set the row index attribute     
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

        clonedRow.find('.start_date').datepicker({ 
			dateFormat: "{{config('constant.js_date_format.date')}}", 
			minDate: 0,
			firstDay: 1,
            onSelect: function(date) {
                var parts = date.split('-'); 
                var selectedDate = new Date(parts[2], parts[1] - 1, parts[0]); 
                
                var currentDate = new Date(); 

                clonedRow.find('.end_date').val('');

                //  set min date of end date
                clonedRow.find(".end_date").datepicker('option', 'minDate', date);

                clonedRow.find(".start_time").val('');
                clonedRow.find(".end_time").val('');                
                
                // set min time of start time
                if (selectedDate.toDateString() === currentDate.toDateString()) {
                    let setEndTime = getCorrectTime(currentDate, 'full_date');                  
                    clonedRow.find(".start_time").timepicker('option', 'minTime', setEndTime.hour + ':' + setEndTime.minute);
                } else {
                    clonedRow.find(".start_time").timepicker('option', 'minTime', null);
                }
            }
		});
         
        clonedRow.find('.end_date').datepicker({ 
			dateFormat: "{{config('constant.js_date_format.date')}}", 
			minDate: 0,
			firstDay: 1,
            onSelect: function(date) {
                var parts = date.split('-'); 
                var endDate = new Date(parts[2], parts[1] - 1, parts[0]); 
                
                var startDate = clonedRow.find(".start_date" ).datepicker( "getDate" );

                clonedRow.find(".start_time").val('');
                clonedRow.find(".end_time").val('');

                // set min time of end time
                if (endDate.toDateString() === startDate.toDateString()) {
                    var start_time = clonedRow.find(".start_time").val();                    
                    let setEndTime = getCorrectTime (start_time);

                    clonedRow.find(".end_time").timepicker('option', 'minTime', setEndTime.hour + ':' + setEndTime.hour);
                } else {
                    clonedRow.find(".end_time").timepicker('option', 'minTime', null);
                }
            }
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

    function getCorrectTime (start_time, type='normal'){
        let nextMinute,hours,minutes;
        if(type == 'normal'){
            let parts = start_time.split(':');
            hours = parts[0];
            minutes = parts[1];
            nextMinute = parseInt(minutes) + step;
        } else {
            hours = start_time.getHours();
            minutes = start_time.getMinutes();
            nextMinute = Math.ceil(minutes / step) * step;
        }

        if(nextMinute == 60){
            hours = parseInt(hours)+1;
            nextMinute = "00";
        }

        return {'hour' : hours, "minute": nextMinute};
    }

</script>