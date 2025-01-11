<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="{{ asset('plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    @if (session('message'))
        toastr.success("{{ session('message') }}");
    @elseif (session('error'))
        toastr.error("{{ session('error') }}");
    @endif

    $(document).ready(function() {
        if (window.parent == window.self) {
            $('.contain-class').addClass('container')
            $('.main_nav').removeClass('d-none');
        } else {
            $('.contain-class').removeClass('container')
            $('.main_nav').addClass('d-none');
        }
    })
</script>
<link rel="stylesheet" type="text/css"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css">

<script type="text/javascript"
    src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>
<script>
    function dispMessage(isError, message, timeout = 10000) {
        try {
            if (isError) {
                toastr.error(message, {
                    timeOut: timeout
                });
            } else {
                toastr.success(message, {
                    timeOut: timeout
                });
            }

        } catch (error) {
            alert(message);
        }
    }

    $(document).on('click', '.close', function() {
        $(this).closest('.modal').modal('hide');
    })

    function deleteMsg(url, target) {
        swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then(function(result) {
            if (result.value) {
                location.href = url;
            }
        })
    }
</script>
