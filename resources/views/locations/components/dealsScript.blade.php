@php($script_type = $script_type ?? 'deal')

<script>

    let vehiclesData = {};
    let baseURL = "{{ route('deals.inventories.search') }}";
    $('.custom_select_vehicle').select2({
        placeholder: 'Select a Vehicle',
        allowClear: true,
        dropdownParent: $("#inventoriesProcessArea"),
        templateResult: formatVehicle, // Custom function for dropdown items
        templateSelection: formatSelection, // Custom function for selected item
        ajax: {
            url: baseURL,
            dataType: 'json',
            delay: 250,
            cache:true,
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
                        vehiclesData[item.id] = item;
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


    $('.custom_select_vehicle').change(function(e){
        let item = vehiclesData[$(this).val()]??null;
        console.log(item);
        if(item){
            setVehicleFields(item)
        }
    })

    function setVehicleFields(item) {
        let vehicleDiv = $(".Vehicle");

        if (vehicleDiv.length === 0) {
            return;
        }
        let inputs = vehicleDiv.find("input"); // Get all input fields inside .Vehicle

        if (inputs.length === 0) {
            return;
        }
        inputs.each(function() {
            let inputField = $(this);
            let classList = inputField.attr("class").split(" "); // Get all classes
            let subKeyClass = classList.find(cls => cls.startsWith("subkey_")); // Find subkey class

            if (subKeyClass) {
                let subKey = subKeyClass.replace("subkey_", "");
                if (typeof item === "undefined") {
                    return;
                }
                let value = item[subKey] !== undefined ? item[subKey] : "";
                inputField.val(value);
            }
        });
    }




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

        var img = item.image ?
            `<img src="${item.image}" style="width:60px; height:60px; border-radius:5px; margin-right:10px;">` :
            '';

        var stock = item.stock ? ` (Stock: ${item.stock})` : '';

        return $(`<span class="d-flex">${img} ${item.text}${stock}</span>`);
    }

    function formatSelection(item) {
        return item.text;
    }


    let contactId = '{{ $contact_id }}'
    let locationId = '{{ $location_id }}'

    $(document).ready(function() {
        @if ($script_type == 'deals')
            getDealsList();
        @else
            $('.create_deal_btn').removeClass('hide');
        @endif
    })

    function getDealsList() {
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
                if (response.error) {
                    toastr.error(response.error);
                    return;
                }
                $('.cus_name').text(response.customer_name);
                if (response.customer_name) {
                    $('.create_deal_btn').removeClass('hide');
                }
                $('.appendData').html(response.view);
                $("#loader-overlay").fadeOut();
            }
        });
    }
    let formData = {}; // Object to store values


    function validateContact() {
        let isValid = true;
        $("#submForm [required]").each(function() {
            let fieldName = $(this).attr("name");
            let fieldValue = $(this).val().trim();

            if (fieldValue == "") {
                isValid = false;
                $(this).addClass("is-invalid"); // Highlight empty fields
            } else {
                $(this).removeClass("is-invalid").addClass("is-valid");
                formData[fieldName] = fieldValue; // Store valid fields in object
            }
        });
        return isValid;
    }





    $(document).on('click', '.create_deal_btn', function() {
        let id = $('.vehicle_field').val();

        if (!id) {
            toastr.error('Please select vehicle first');
            return;
        }

        @if ($script_type == 'form')
            let contactId = $('.contact').val();
            if (!contactId && !validateContact()) {
                toastr.error('Please select contact or create a new one all fields are required');
                return;
            }
        @endif

        $("#loader-overlay").css("display", "flex").hide().fadeIn(); // Ensures hidden first, then fades in

        $.ajax({
            type: 'GET',
            data: {
                vehicle_id: id,
                formData: formData,
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


    $(document).on('click', ".contact_create", function() {
        var offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasForm'));
        offcanvas.show(); // Open the sidebar
    });

    $(document).on('click','.contact_field_form',function(){
        $("#loader-overlay").css("display", "flex").hide().fadeIn(); // Ensures hidden first, then fades in
        validateContact();
        let is_tag = $(this).closest('form').data('tag');
        $.ajax({
            type: 'GET',
            data: {
                contactId: contactId,
                locationId: locationId,
                formData:formData,
                is_tag:is_tag,
            },
            url: '{{ route('manage.conatct.fields') }}',
            success: function(response) {
                $("#loader-overlay").fadeOut();
                if(response.success)
                {
                   toastr.success('Updated Successfully');
                }
                if(response.error)
                {
                   toastr.error('there is something wrong');
                }
            }
        })
    })
</script>


