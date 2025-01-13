<script>

     let dealId = '{{ $deal_id }}'
     let locationId = '{{ $location_id }}'
     let contactId = '{{ $location_id }}'

    $('.custom_select').select2({
        placeholder: 'Select a Vehicle',
        allowClear: true,
        dropdownParent: $("#processArea"),
        ajax: {
            url: "{{ route('coborrower.contacts.search') }}",
            data:{locationId: locationId},
            dataType: 'json',
            delay: 250,
            processResults: function(data) {
                console.log(data);
                return {
                    results: [].concat($.map(data, function(item) {
                        return {
                            text: item.name,
                            id: item.id
                        };
                    }))
                };
            },
            cache: true
        }
    });




    $(document).on('change', '.contact', function() {
        let contactId = $(this).val();

        $.ajax({
            type: 'GET',
            data: {
                id: contactId,
                locationId: locationId,
                dealId: dealId,
            },
            url: '{{ route('coborrower.get.customer') }}',
            success: function(response) {
                if(response.customer_id)
            {
                confirmation(response.customer_id);
            }
            else{
                toastr.error('there is an issue');
            }
            }
        });

    })

    function confirmation(customer_id) {
                    swal.fire({
                        title: 'Are you sure?',
                        text: "You want to add this contact as coborrower in your deal ?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, make it!',
                        cancelButtonText: 'No, cancel!',
                        reverseButtons: true
                    }).then(function(result) {
                        $.ajax({
            type: 'GET',
            data: {
                contactId: contactId,
                locationId: locationId,
                dealId: dealId,
                customer_id: customer_id,

            },
            url: '{{ route('coborrower.set.deal') }}',
            success: function(response) {
                if(response.success)
            {
                toastr.success(response.success)

            }else{
                toastr.error(response.error)
            }
            }
        });
                    });
                }
    // -
</script>
