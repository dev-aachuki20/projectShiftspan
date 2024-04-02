<div class="sidebar_overlay d-lg-none"></div>
<!-- Jquery Library -->
<script src="{{ asset('js/jquery.min.js') }}"></script>
<!-- Bootstrap Js -->
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<!-- Custom Js -->
<script src="{{ asset('js/custom.js') }}"></script>


@include('partials.alert')

<!-- datatable -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>

@php
$arr = [
    "sZeroRecords" => __('cruds.datatable.data_not_found'),
    "sProcessing" => '<img src="'.(asset(config('constant.default.datatable_loader'))).'" width="100"/>',
    "sLengthMenu" => __('cruds.datatable.show') . " _MENU_ " . __('cruds.datatable.entries'),
    "sInfo" => config('app.locale') == 'en' ?
        __('cruds.datatable.showing') . " _START_ " . __('cruds.datatable.to') . " _END_ " . __('cruds.datatable.of') . " _TOTAL_ " . __('cruds.datatable.entries') :
        __('cruds.datatable.showing') . "_TOTAL_" . __('cruds.datatable.to') . __('cruds.datatable.of') . "_START_-_END_" . __('cruds.datatable.entries'),
    "sInfoEmpty" => __('cruds.datatable.showing') . " 0 " . __('cruds.datatable.to') . " 0 " . __('cruds.datatable.of') . " 0 " . __('cruds.datatable.entries'),
    "search" => __('cruds.datatable.search'),
    "paginate" => [
        "first" => __('cruds.datatable.first'),
        "last" => __('cruds.datatable.last'),
        "next" => __('cruds.datatable.next'),
        "previous" => __('cruds.datatable.previous'),
    ],
    "autoFill" => [
        "cancel" => __('message.cancel'),
    ],
];

$jsonArr = json_encode($arr);
@endphp

<script>
    // Custom select box
    $(document).on('click', '.select-styled', function() {
		$('.select-options').not($(this).next('.select-options')).slideUp();
		$(this).next('.select-options').slideToggle();
	});
    
    document.addEventListener('shown.bs.modal', function(event) {
        const modal = bootstrap.Modal.getInstance(event.target);
        // Update the backdrop option to "static"
        modal._config.backdrop = 'static';
    });

    // Password field hide/show functiolity
    $(document).on('click', '.toggle-password',function () {        
        var passwordInput = $(this).prev('input');        
        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            $(this).removeClass('close-eye').addClass('open-eye');
        } else {
            passwordInput.attr('type', 'password');
            $(this).removeClass('open-eye').addClass('close-eye');
        }
    });
    
    $( document ).ajaxError(function( event, response, settings ) {
        if(response.status == 401){
            window.location.href = "{{ route('login') }}";
        }
    });

    // Datatable global default configuration
    $(document).ready(function(e){
        (function ($, DataTable) {
            $.extend(true, DataTable.defaults, {
                'responsive': true,
                "scrollCollapse" : true,
                'autoWidth' : true,
                language: {!! $jsonArr !!}
            });
        })(jQuery, jQuery.fn.dataTable);
    });

    
    
    $(document).on('change', '#dt_cb_all', function(e){
        var t = $(this);
        if(t.prop('checked') === true){
            $('.dt_cb').prop('checked', true);
        } else {
            $('.dt_cb').prop('checked', false);
        }
    });

    $(document).on('change', '.dt_cb', function(e){    
        if ($('.dt_cb:checked').length == $('.dt_cb').length) {
            $('#dt_cb_all').prop('checked', true);
        } else {
            $('#dt_cb_all').prop('checked', false);
        }
    });

    function updateHeaderProfile(profile_image, user_name){
        if(profile_image != ''){
            $('#header_profile_image').removeClass('default-image');
            $('#header_profile_image').attr('src', profile_image);
        }
        $('#header_auth_name').text(user_name);
    }
    
    $(document).on('click', '.notificationsBtn', function () {
        getNotifications();
    });

    function getNotifications() {
        // setTimeout(() => {
        //     $('.loader-div').show();
        // }, 100);       
        $.ajax({
            type: 'get',
            url: "{{ route('getNotification') }}",
            dataType: 'json',
            success: function (response) {
                if(response.success) {
                    $('.notifications_area').html(response.htmlView);
                    // setTimeout(() => {
                    //     $('.loader-div').hide();
                    // }, 100); 
                }
            },
            error: function (response) {
                if(response.responseJSON.error_type == 'something_error'){
                    toasterAlert('error',response.responseJSON.error);
                    // setTimeout(() => {
                    //     $('.loader-div').hide();
                    // }, 100); 
                } 
            },
        });
    }

    function markAsRead(notify_id){
       /* $.ajax({
            type: 'get',
            url: "{{route('read.notification')}}",
            dataType: 'json',
            data:{
                _token: "{{ csrf_token() }}",
                notification: notify_id,   
            },
            success: function (response) {
                console.log(response);
                if(response.success == true){
                    // setTimeout(() => {
                    //     $('.loader-div').hide();
                    //     getNotifications();
                    // }, 100); 

                    getNotifications();
                }
            },
            error: function (response) {
                if(response.responseJSON.error_type == 'something_error'){
                    toasterAlert('error',response.responseJSON.error);
                    // setTimeout(() => {
                    //     $('.loader-div').hide();
                    // }, 100); 
                } 
            },
        }); */
    }

    @can('staff_view')
        $(document).on("click",".viewStaffBtn", function($type) {
            event.preventDefault();
            $('.loader-div').show();

            var url = $(this).data('href');
            var type = $(this).data('type');
            $.ajax({
                type: 'get',
                url: url,
                data: {
                    'type' : type
                },
                dataType: 'json',
                success: function (response) {

                    if(response.success) {
                        $('.popup_render_div').html(response.htmlView);
                        $('#staffDetails').modal('show');
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
    @endcan

</script>