<script>
    $(document).on('click', '.del-btn-ajax', function(e) {
        e.preventDefault();
        let target = $(this).closest('tr');
        let rou = $(this).attr('href');
        deleteMsg(rou, target);
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
                if (target) {
                    target.remove();
                    removeByAjax(url);
                } else {
                    location.href = url;
                }
            }
        })
    }
</script>
