
@php($script_type = $script_type ?? 'deal')

<script>
    $('.custom_select_vehicle').select2({
    placeholder: 'Select a Vehicle',
    allowClear: true,
    dropdownParent: $("#inventoriesProcessArea"),
    templateResult: formatVehicle, // Custom function for dropdown items
    templateSelection: formatSelection, // Custom function for selected item
    ajax: {
        url: "{{ route('deals.inventories.search') }}",
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
                        id: item.id,
                        text: item.name, // Required for Select2
                        image: item.image, // Include image URL
                        stock: item.stock // Include stock count
                    };
                })
            };
        },
        cache: true
    }
});


$('.custom_select').select2({
    placeholder: 'Select a Contact',
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

function formatVehicle(item) {
    if (!item.id) return item.text; // For the placeholder

    var img = item.image
        ? `<img src="${item.image}" style="width:60px; height:60px; border-radius:5px; margin-right:10px;">`
        : '';

    var stock = item.stock ? ` (Stock: ${item.stock})` : '';

    return $(`<span class="d-flex">${img} ${item.text}${stock}</span>`);
}

function formatSelection(item) {
    return item.text;
}


    let contactId = '{{ $contact_id }}'
    let locationId = '{{ $location_id }}'

    $(document).ready(function() {
        @if($script_type == 'deals')
            getDealsList();
        @endif
    })

    function getDealsList()
    {
        $("#loader-overlay").css("display", "flex").hide().fadeIn(); // Ensures hidden first, then fades in

$.ajax({
    type: 'GET',
    data: {
        contactId: contactId,
        locationId: locationId,
    },
    url: '{{ route('deals.get.list') }}',
    success: function(response) {
        console.log(response);
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
        $("#loader-overlay").fadeOut();
    }
});
    }



    $(document).on('click', '.create_deal_btn', function() {
        let id = $('.vehicle_field').val();

        if(!id)
    {
        toastr.error('Please select vehicle first');
        return ;
    }
    $("#loader-overlay").css("display", "flex").hide().fadeIn(); // Ensures hidden first, then fades in

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
                toastr.success('Successfully Created');
                getDealsList();
            }
        });

    })


</script>
