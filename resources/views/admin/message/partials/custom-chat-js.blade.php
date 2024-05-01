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

            $('.loader-div').show();

            var formData = new FormData(this);

            $.ajax({
                type: 'post',
                url: "{{route('messages.store')}}",
                dataType: 'json',
                contentType: false,
                processData: false,
                data: formData,
                success: function (response) {
                    $('.loader-div').hide();
                    $(".submitBtn").attr('disabled', false);
                    if(response.success) {
                        $('#addNotificationForm')[0].reset();
                        $('#NnotificationSettings').modal('hide');

                        var selected = [];
                        selected.push($(this).closest(".select-option").find('span').text());
                        $('.selected-options').text(selected.length > 0 ? 'Select...' : 'Select...');

                        getAllGroups();
                        getChatBox();
                        
                        // $('.popup_render_div').html('');
                        // $('#message-centre-table').DataTable().ajax.reload(null, false);
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
                            errorLabelTitle = '<span class="validation-error-block">'+item[0]+'</sapn>';
                            if (key.indexOf('staffs') !== -1) {
                                $(".staffs_error").html(errorLabelTitle);
                            } else if(key== 'section') {
                                $(".section_error").html(errorLabelTitle);
                            } else if(key== 'subject') {
                                $(".subject_error").html(errorLabelTitle);
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
        $('#addNotificationForm')[0].reset();
        $('#addNotificationForm .selected-options').html('');
        $('.staff-checkbox').attr('checked',false);
        $('.validation-error-block').remove();
    });

    $(document).on('input','#groupSearch',function(e){
        e.preventDefault();

        var searchVal = $(this).val();

        getAllGroups(searchVal);

    });

    $(document).on('click','.groupItem',function(e){
        e.preventDefault();

        var $this = $(this);

        $('.groupItem').removeClass('active');

        $this.addClass('active');

        var groupId = $this.attr('data-listId');

        getChatBox(groupId);
        
    });

    $(document).on('submit','#messageInputForm',function(e){
        e.preventDefault();

        var $this = $(this);

        if($this.find('textarea').val() != ''){
            $("#messageInputForm .submitBtn").attr('disabled', true);

            var formData = new FormData(this);

            $.ajax({
                type: $this.attr('method'),
                url: $this.attr('action'),
                dataType: 'json',
                contentType: false,
                processData: false,
                data: formData,
                success: function (response) {
                    
                    $("#messageInputForm .submitBtn").attr('disabled', false);
                    if(response.success) {
                        $('#messageInputForm')[0].reset();

                        $('#messageContainer').append(response.viewMessageHtml);

                        toasterAlert('success',response.message);
                    }
                },
                error: function (response) {
                    $("#messageInputForm .submitBtn").attr('disabled', false);

                    if(response.responseJSON.error_type == 'something_error'){
                        toasterAlert('error',response.responseJSON.error);
                    } else {
                        // var errorLabelTitle = '';
                        // $.each(response.responseJSON.errors, function (key, item) {
                        //     errorLabelTitle = '<span class="validation-error-block">'+item[0]+'</sapn>';
                        //     if (key.indexOf('staffs') !== -1) {
                        //         $(".staffs_error").html(errorLabelTitle);
                        //     } else if(key== 'section') {
                        //         $(".section_error").html(errorLabelTitle);
                        //     } else if(key== 'subject') {
                        //         $(".subject_error").html(errorLabelTitle);
                        //     } else{
                        //         $(document).find('[name='+key+']').after(errorLabelTitle);
                        //     }
                        // });
                    }
                },
                complete: function(res){
                    $("#messageInputForm .submitBtn").attr('disabled', false);
                }
            });
        }
       
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

    async function getAllGroups(searchVal=''){
        $('.group-list').html('');
        $('.sidebar-loader').show();

        $.ajax({
            type: 'get',
            url: "{{ route('messages.getGroupList') }}",
            dataType: 'json',
            data: {'search':searchVal},
            success: function (response) {
                $('.sidebar-loader').hide();
                if(response.success){
                    $('.group-list').html(response.htmlView);
                }
            },
            error: function (response) {
                // $('.sidebar-loader').hide();
                console.log('Error Response-',response);
                // if(response.responseJSON.error_type == 'something_error'){
                //     toasterAlert('error',response.responseJSON.error);
                    // $('.loader-div').hide();
                // } 
            },
            complete: function(res){
                $('.sidebar-loader').hide();
            }
        });
    }

    async function getChatBox(groupId=null){
        $('.groupChatScreen').html('');
        $('.screen-loader').show();

        $.ajax({
            type: 'get',
            url: "{{ route('messages.showChatScreen') }}",
            dataType: 'json',
            data: {'groupId':groupId},
            success: function (response) {
                $('.screen-loader').hide();

                if(response.success){
                    $('.totalUnreadMess').remove();
                    $('.groupChatScreen').html(response.htmlView);
                }
            },
            error: function (response) {
                // $('.screen-loader').hide();

                console.log('Error Response-',response);
                // if(response.responseJSON.error_type == 'something_error'){
                //     toasterAlert('error',response.responseJSON.error);
                    // $('.loader-div').hide();
                // } 
            },
            complete: function(res){
                $('.screen-loader').hide();
            }
        });
    }
</script>
