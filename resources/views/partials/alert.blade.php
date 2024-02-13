<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    
    @if (session()->has('status'))
        toasterAlert('success',"{{ session()->get('status') }}");
    @endif

    @if (session()->has('success'))
        toasterAlert('success',"{{ session()->get('success') }}");
    @endif

    @if (session()->has('error'))
        toasterAlert('error',"{{ session()->get('error') }}");
    @endif

    @if (session()->has('warning'))
        toasterAlert('warning',"{{ session()->get('warning') }}");
    @endif

    @if (session()->has('info'))
        toasterAlert('info',"{{ session()->get('info') }}");
    @endif
    
    function toasterAlert(status, message) {

        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            // showCancelButton :true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        Toast.fire({
            icon: status,
            title: message
        });

    }
</script>