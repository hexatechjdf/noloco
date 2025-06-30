@php($script_type = $script_type ?? 'deal')

<script>
    let sold = false;
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
            cache: true,
            data: function(params) {
                return {
                    locationId: locationId,
                    term: params.term, // Send the search term to the server
                    sold: sold // Send the search term to the server
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


    $('.custom_select_vehicle').change(function(e) {
        let item = vehiclesData[$(this).val()] ?? null;
        console.log(item);
        if (item) {
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
        if (!item.id) {
            return item.text; // For placeholder
        }

        let image = item.image ? item.image : '{{ asset('assets/images/dummy_car.png') }}';
        let $vehicle = $(`
        <div class="injected-vehicle-card" style="display: flex; align-items: center; gap: 10px;">
            <img src="${image}" class="vehicle-card-img" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
            <div class="vehicle-card-details">
                <strong>${item.text}</strong><br>
                Stock: ${item.stock}
            </div>
        </div>
    `);
        return $vehicle;
    }

    function _formatVehicle(item) {
        if (!item.id) return item.text; // For the placeholder
        let imgg = item.image ??
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS2TgOv9CMmsUzYKCcLGWPvqcpUk6HXp2mnww&s';
        var img =
            `<img src="${imgg}" style="width:60px; height:60px; border-radius:5px; margin-right:10px;">`;

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


    // function validateContact() {
    //     let isValid = true;
    //     $("#submForm [required]").each(function() {
    //         let fieldName = $(this).attr("name");
    //         let fieldValue = $(this).val().trim();

    //         if (fieldValue == "") {
    //             isValid = false;
    //             $(this).addClass("is-invalid"); // Highlight empty fields
    //         } else {
    //             $(this).removeClass("is-invalid").addClass("is-valid");
    //             formData[fieldName] = fieldValue; // Store valid fields in object
    //         }
    //     });
    //     return isValid;
    // }


    function validateContact() {
        let isValid = true;
        const fd = new FormData(); // 🔁 renamed from formData to fd

        $("#submForm [required]").each(function() {
            const $field = $(this);
            const fieldName = $field.attr("name");
            const type = $field.attr("type");

            if (!fieldName) return;

            if (type === "file") {
                if (this.files && this.files.length > 0) {
                    const file = this.files[0];
                    if (file) {
                        $field.removeClass("is-invalid").addClass("is-valid");
                        fd.append(fieldName, file, file.name);
                    } else {
                        isValid = false;
                        $field.addClass("is-invalid");
                    }
                } else {
                    isValid = false;
                    $field.addClass("is-invalid");
                }
            } else {
                const value = $field.val().trim();
                if (value === "") {
                    isValid = false;
                    $field.addClass("is-invalid");
                } else {
                    $field.removeClass("is-invalid").addClass("is-valid");
                    fd.append(fieldName, value);
                }
            }
        });

        return {
            isValid,
            formData: fd
        };
    }



    $(document).on('click', '.create_deal_btn', function() {
        let id = $('.vehicle_field').val();

        if (!id) {
            toastr.error('Please select vehicle first');
            return;
        }
        let result = validateContact();
        @if ($script_type == 'form')
            let contactId = $('.contact').val();
            if (!contactId && !result.isValid) {
                toastr.error('Please select contact or create a new one all fields are required');
                return;
            }
        @endif
        let formData = result.formData;
        formData.append('contactId', contactId);
        formData.append('locationId', locationId);
        formData.append('vehicle_id', id);

        $("#loader-overlay").css("display", "flex").hide().fadeIn();

        $.ajax({
            type: 'POST',
            data: formData,
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
        offcanvas.show();
    });

    $(document).on('click', '.contact_field_form', function() {
        $("#loader-overlay").css("display", "flex").hide().fadeIn();
        let result = validateContact();
        let formData = result.formData;

        let is_tag = $(this).closest('form').data('tag');

        formData.append('contactId', contactId);
        formData.append('locationId', locationId);
        formData.append('is_tag', is_tag);

        $.ajax({
            type: 'POST',
            data: formData,
            url: '{{ route('manage.conatct.fields') }}',
            contentType: false,
            processData: false,
            success: function(response) {
                $("#loader-overlay").fadeOut();
                if (response.success) {
                    toastr.success('Updated Successfully');
                }
                if (response.error) {
                    toastr.error('there is something wrong');
                }
            }
        })
    })

    $(document).on('change', '.sold-vehicle-condition', function() {
        let isChecked = $(this).prop('checked'); // ✅ true or false
        let value = $(this).val();

        if (isChecked) {
            sold = true;
        } else {
            sold = false;
        }
    });
</script>
