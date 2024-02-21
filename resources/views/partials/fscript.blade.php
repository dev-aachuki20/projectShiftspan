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


<script>
    $( document ).ajaxError(function( event, response, settings ) {
        if(response.status == 401){
            window.location.href = "{{ route('admin/login') }}";
        }
    });
</script>