<script>
    $('.custom_select').select2({
        placeholder: 'Select a Vehicle',
        allowClear: true,
        dropdownParent: $("#processArea"),
        ajax: {
            url: "{{ route('coborrower.contacts.search') }}",
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

    let dealId = '{{ $deal_id }}'
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
                $('.cus_name').text(response.customer_name);
                if (response.customer_name) {
                    $('.create_deal_btn').removeClass('hide');
                }
                $('.appendData').html(response.view);
            }
        });
    })

    $(document).on('change', '.customer', function() {
        let id = $(this).val();

        $.ajax({
            type: 'GET',
            data: {
                id: id
            },
            url: '{{ route('deals.get.deals') }}',
            success: function(response) {
                console.log(response);
            }
        });

    })
    // -
</script>
