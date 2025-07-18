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


    // function validateContact() {
    //     let isValid = true;
    //     const fd = new FormData(); // 🔁 renamed from formData to fd

    //     $("#submForm [required]").each(function() {
    //         const $field = $(this);
    //         const fieldName = $field.attr("name");
    //         const type = $field.attr("type");

    //         if (!fieldName) return;

    //         if (type === "file") {
    //             if (this.files && this.files.length > 0) {
    //                 const file = this.files[0];
    //                 if (file) {
    //                     $field.removeClass("is-invalid").addClass("is-valid");
    //                     fd.append(fieldName, file, file.name);
    //                 } else {
    //                     isValid = false;
    //                     $field.addClass("is-invalid");
    //                 }
    //             } else {
    //                 isValid = false;
    //                 $field.addClass("is-invalid");
    //             }
    //         } else {
    //             const value = $field.val().trim();
    //             if (value === "") {
    //                 isValid = false;
    //                 $field.addClass("is-invalid");
    //             } else {
    //                 $field.removeClass("is-invalid").addClass("is-valid");
    //                 fd.append(fieldName, value);
    //             }
    //         }
    //     });

    //     return {
    //         isValid,
    //         formData: fd
    //     };
    // }

    // function validateContact() {
    //     let isValid = true;
    //     const fd = new FormData();
    //     const errors = []; // 🔁 naya array for messages

    //     // simple regexes ─ zaroorat ke mutabiq customise kar sakte ho
    //     const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    //     const PHONE_RE = /^\+?\d{10,15}$/; // 10‑15 digits, optional +

    //     $("#submForm [required]").each(function() {
    //         const $field = $(this);
    //         const fieldName = $field.attr("name");
    //         const type = ($field.attr("type") || "").toLowerCase();

    //         if (!fieldName) return; // skip agar name hi nahī̃

    //         // FILE INPUT
    //         if (type === "file") {
    //             const file = this.files?.[0];
    //             if (file) {
    //                 $field.removeClass("is-invalid").addClass("is-valid");
    //                 fd.append(fieldName, file, file.name);
    //             } else {
    //                 isValid = false;
    //                 errors.push({
    //                     field: fieldName,
    //                     message: "File is required"
    //                 });
    //                 $field.addClass("is-invalid");
    //             }
    //             return; // ⬅ file handle ho gaya
    //         }

    //         // TEXT‑BASED INPUTS
    //         const value = $.trim($field.val());

    //         // Blank check
    //         if (value === "") {
    //             isValid = false;
    //             errors.push({
    //                 field: fieldName,
    //                 message: "This field is required"
    //             });
    //             $field.addClass("is-invalid");
    //             return;
    //         }

    //         // Type‑specific validation
    //         let typeError = "";
    //         if (type === "email" && !EMAIL_RE.test(value)) {
    //             typeError = "Invalid email address";
    //         } else if ((type === "tel" || fieldName.toLowerCase().includes("phone")) && !PHONE_RE.test(value)) {
    //             const phoneInput = document.querySelector('.phone').value;
    //             try {
    //                 const phoneNumber = libphonenumber.parsePhoneNumber(phoneInput);

    //                 if (!phoneNumber.isValid()) {
    //                     isValid = false;
    //                     errors.push({
    //                         field: fieldName,
    //                         message: 'Invalid phone number format'
    //                     });
    //                     $field.addClass("is-invalid");
    //                 }
    //             } catch (e) {
    //                 isValid = false;
    //                 errors.push({
    //                     field: fieldName,
    //                     message: 'Invalid phone number format'
    //                 });
    //                 $field.addClass("is-invalid");
    //             }
    //         }

    //         if (typeError) {
    //             isValid = false;
    //             errors.push({
    //                 field: fieldName,
    //                 message: typeError
    //             });
    //             $field.addClass("is-invalid");
    //         } else {
    //             $field.removeClass("is-invalid").addClass("is-valid");
    //             fd.append(fieldName, value);
    //         }
    //     });

    //     return {
    //         isValid,
    //         formData: fd,
    //         errors // ⬅ array of {field, message}
    //     };
    // }

    function validateContact() {
        let isValid = true;
        const fd = new FormData();
        const errors = []; // Array for error messages

        // Simple regexes for validation
        const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const PHONE_RE = /^\+?\d{10,15}$/; // 10-15 digits, optional +

        // Read all fields (required or not)
        $("#submForm :input").each(function() {
            const $field = $(this);
            const fieldName = $field.attr("name");
            const type = ($field.attr("type") || "").toLowerCase();
            const isRequired = $field.prop("required");

            if (!fieldName) return; // Skip if no name attribute

            // Handle file inputs
            if (type === "file") {
                const file = this.files?.[0];
                if (file) {
                    $field.removeClass("is-invalid").addClass("is-valid");
                    fd.append(fieldName, file, file.name);
                } else if (isRequired) {
                    isValid = false;
                    errors.push({
                        field: fieldName,
                        message: "File is required"
                    });
                    $field.addClass("is-invalid");
                }
                return;
            }

            // Handle text-based inputs
            const value = $.trim($field.val());

            // Append non-empty values to FormData (required or not)
            if (value !== "") {
                fd.append(fieldName, value);
            }

            // Validation for required fields only
            if (isRequired) {
                // Blank check
                if (value === "") {
                    isValid = false;
                    errors.push({
                        field: fieldName,
                        message: "This field is required"
                    });
                    $field.addClass("is-invalid");
                    return;
                }

                // Type-specific validation
                let typeError = "";
                if (type === "email" && !EMAIL_RE.test(value)) {
                    typeError = "Invalid email address";
                } else if ((type === "tel" || fieldName.toLowerCase().includes("phone")) && !PHONE_RE.test(
                        value)) {
                    try {
                        const phoneNumber = libphonenumber.parsePhoneNumber(value);
                        if (!phoneNumber.isValid()) {
                            typeError = "Invalid phone number format";
                        }
                    } catch (e) {
                        typeError = "Invalid phone number format";
                    }
                }

                if (typeError) {
                    isValid = false;
                    errors.push({
                        field: fieldName,
                        message: typeError
                    });
                    $field.addClass("is-invalid");
                } else {
                    $field.removeClass("is-invalid").addClass("is-valid");
                }
            } else if (value !== "") {
                // Non-required fields with data get is-valid class
                $field.removeClass("is-invalid").addClass("is-valid");
            }
        });

        return {
            isValid,
            formData: fd,
            errors
        };
    }




    $(document).on('click', '.create_deal_btn', function() {
        let id = $('.vehicle_field').val();
        alert($script_type);
        alert($script_type);
        return 123;
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

    function buttontoLoader(button) {
        // Change button to loading state
        button.html(`
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Loading...
        `);
        button.prop('disabled', true);
    }

    function loaderToButton(button, originalContent) {
        button.html(originalContent);
        button.prop('disabled', false);
    }

    $(document).on('click', '.contact_field_form', function() {

        var button = $(this);
        var originalContent = button.html();

        buttontoLoader(button);
        // $("#loader-overlay").css("display", "flex").hide().fadeIn();
        let result = validateContact();

        if (!result.isValid) {
            toastr.clear();
            let errors = result.errors;
            errors.forEach(err => {
                // Title optional hai — field name ko human‑friendly bana diya
                const title = err.field.replace(/_/g, " ").replace(/\b\w/g, l => l.toUpperCase());
                toastr.error(err.message, title); // toastr.error(body, title)
            });

            loaderToButton(button, originalContent);
            return; // submit abort
        }

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
                loaderToButton(button, originalContent);
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

    function getImageUrlFromContact(contact, key) {
        let firstUrl = null;
        let coss = contact[key];
        if (coss) {
            let firstKey = Object.keys(coss)[0];
            if (firstKey && coss[firstKey].url) {
                firstUrl = coss[firstKey].url;
                if (firstUrl) {
                    let htmll = `<a href="${firstUrl}" target="_blank"><img class="down-image h-20p"
                                                src="{{ asset('assets/images/down.png') }}"></a>`;
                    $('.' + key + '_box').html(htmll);
                }
                console.log(firstUrl);
            }
        }
    }
</script>
