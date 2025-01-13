<script>
    $('.custom_select').select2({
        placeholder: 'Select a Vehicle',
        allowClear: true,
        dropdownParent: $("#inventoriesProcessArea"),
        ajax: {
            url: "{{ route('deals.inventories.search') }}",
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

    let contactId = '{{ $contact_id }}'
    let locationId = '{{ $location_id }}'

    $(document).ready(function() {
        $.ajax({
            type: 'GET',
            data: {
                contactId: contactId,
                locationId: locationId,
            },
            url: '{{ route('deals.get.customers') }}',
            success: function(response) {
                if(response.error)
            {
                toastr.error(response.error);
                return ;
            }
                $('.cus_name').text(response.customer_name);
                if(response.customer_name)
            {
                $('.create_deal_btn').removeClass('hide');
            }
                $('.appendData').html(response.view);
            }
        });
    })



    $(document).on('click', '.create_deal_btn', function() {
        let id = $('.vehicle_field').val();
        if(!id)
    {
        toastr.error('Please select vehicle first');
        return ;
    }
        $.ajax({
            type: 'GET',
            data: {
                vehicle_id: id,
                contactId: contactId,
                locationId: locationId,
            },
            url: '{{ route('deals.create.setting') }}',
            success: function(response) {
                console.log(response);
            }
        });

    })


    // $(document).on('change', '.customer', function() {
    //     let id = $(this).val();

    //     $.ajax({
    //         type: 'GET',
    //         data: {
    //             id: id
    //         },
    //         url: '{{ route('deals.get.deals') }}',
    //         success: function(response) {
    //             console.log(response);
    //         }
    //     });

    // })
    // -
</script>
