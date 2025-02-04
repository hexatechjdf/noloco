<script>

     let dealId = '{{ $deal_id }}'
     let locationId = '{{ $location_id }}'
     let contactId = '{{ @$contact_id }}'

     $('.custom_select').select2({
    placeholder: 'Select a Coborrower',
    allowClear: true,
    dropdownParent: $("#processArea"),
    ajax: {
        url: "{{ route('coborrower.contacts.search') }}",
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                locationId: locationId,
                term: params.term // Send the search term to the server
            };
        },
        processResults: function(data) {
            console.log(data);
            return {
                results: $.map(data, function(item) {
                    return {
                        text: item.name,
                        id: item.id
                    };
                })
            };
        },
        cache: true
    }
});





    $(document).on('change', '.contact', function() {
        contactId = $(this).val();
        confirmation();
        return ;
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

    function confirmation() {
                    swal.fire({
                        title: 'Are you sure?',
                        text: "You want to add this contact as coborrower in your deal ?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, make it!',
                        cancelButtonText: 'No, cancel!',
                        reverseButtons: true
                    }).then(function(result) {
                        if(result.value)
                    {
                        $("#loader-overlay").css("display", "flex").hide().fadeIn(); // Ensures hidden first, then fades in
                        $.ajax({
            type: 'GET',
            data: {
                contactId: contactId,
                locationId: locationId,
                dealId: dealId,
            },
            url: '{{ route('coborrower.set.deal') }}',
            success: function(response) {
                if(response.success)
            {
                toastr.success(response.success)

            }else{
                toastr.error(response.error)
            }
            $("#loader-overlay").fadeOut();
            }
        });
                    }
                    });
                }
    // -
</script>
