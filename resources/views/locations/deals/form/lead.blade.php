@extends('layouts.app')
@push('style')
    <link href="{{ asset('admin/assets/dashboard/css/select2.min.css') }}" rel="stylesheet">
    <style>
        .select2-container {
            width: 100% !important;
            /* z-index: 999999999999999999; */
        }

        div.dataTables_wrapper div.dataTables_length select {
            width: 100% !important;
        }

        .show_values {
            cursor: pointer;
        }

        .add_pill {
            padding: 2px 6px 4px 6px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f2f4f7;
            margin: 0;
        }

        .ptc-100 {
            padding-top: 10px;
        }

        .main-card {
            background: white;
            min-width: 0;
            padding: 32px 36px 28px 36px;
            border-radius: 14px;
            box-shadow: 0 7px 32px rgba(0, 0, 0, 0.09);
        }

        .form-rows [class^="col-"] {
            margin-top: 10px;
        }

        .collapse-head {
            font-weight: bold;
            font-size: 1.5rem;
        }

        .accordion-item {
            border: none;
        }

        .accordion-header {
            background: linear-gradient(90deg, #eaf1fb 0%, #fafdff 100%);
            box-shadow: 0 2px 8px rgba(24, 106, 214, 0.07);
            border: 1.5px solid #bdd2e2;
        }

        .accordion-button {
            background: linear-gradient(90deg, #eaf1fb 0%, #fafdff 100%);
        }

        .accordion-body {
            padding: 0px;
        }

        .injected-vehicle-card {
            padding: 5px 0;
        }

        .vehicle-card-img {
            border: 1px solid #ddd;
        }

        .vehicle-card-details {
            font-size: 13px;
            color: #333;
        }

        #qr-block canvas {
            margin: auto;
        }

        .input-fil-custom {
            padding: 7px 6px;
            font-size: 15px;
            background: #fafdff;
        }

        .h-20p {
            height: 20px;
        }

        label {
            justify-content: space-between;
        }

        .suggestions-box {
            border: 1px solid #ccc;
            max-height: 200px;
            overflow-y: auto;
            display: none;
            background: white;
            width: 100%;
            z-index: 9999;
        }

        .suggestion-item {
            padding: 8px;
            cursor: pointer;
        }

        .suggestion-item:hover {
            background: #f0f0f0;
        }
    </style>
@endpush
@section('content')
    <div class="container ">

        <form id="submForm" class="mt-3" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-12">
                    <div class="">
                        <div class=" oppertunitiesBox">

                        </div>
                    </div>
                </div>
                <div class="col-md-8 contactFields">
                    <div class="appendData">
                        <div class="form-boxx">
                            @include('forms.internals.contactForm', [
                                'heading' => 'Vehicle',
                                'cols' => 'col-md-4',
                                'allowed_types' => ['simple'],
                                'form' => vehicleForm(),
                            ])

                            <div class="card  radius-10 main-card">
                                <div class="card-body">
                                    <div class="card-title">
                                        <h5 class="">Contact Fields</h5>
                                    </div>
                                    @include('forms.internals.fieldsSetting', [
                                        'heading' => 'Contact',
                                        'cols' => 'col-md-4',
                                        'is_autocomplete' => true,
                                        'allowed_types' => ['simple', 'extra', 'vs'],
                                        'form' => contact_informationForm(),
                                    ])

                                    <div class="card-title mt-3">
                                        <h5 class="">Identification</h5>
                                    </div>
                                    @include('forms.internals.fieldsSetting', [
                                        'heading' => 'Contact',
                                        'cols' => 'col-md-4',
                                        'allowed_types' => ['simple', 'extra'],
                                        'form' => identificationForm(),
                                    ])
                                    <button class="btn btn-primary mt-3 contact_field_form" type="button">Submit</button>
                                </div>
                            </div>

                        </div>
                    </div>
                    @include('components.loader')
                </div>
                <div class="col-md-4">
                    <div class="card  radius-10 main-card ">
                        <div class="card-body">
                            @include('locations.components.vehicleFields', [
                                'is_source' => true,
                                'is_full' => '12',
                            ])


                            <div class="card-title mt-3">
                                <h5 class="mb-0">Document Uploads</h5>
                            </div>

                            <div class="">
                                <label class="form-label d-flex">Driver's License
                                    <div class="drivers_licence_box">
                                    </div>
                                </label>
                                <input class="form-control input-fil-custom" type="file" id="drivers_license"
                                    name="drivers_licence" accept="image/*,application/pdf" required>
                            </div>

                            <div class="mt-3">
                                <label class="form-label d-flex">Insurance Card
                                    <div class="insurance_card_box">
                                    </div>

                                </label>
                                <input class="form-control input-fil-custom" type="file" id="insurance_card"
                                    name="insurance_card" accept="image/*,application/pdf" required>
                            </div>
                        </div>


                        {{-- <div id="qrArea" class="mt-3" style="display: none;">
                            <div id="qrCodeContainer" class="mb-2"></div>
                            <div id="qrExpiredMsg" class="text-danger fw-bold" style="display: none;">
                                QR Code expired. Please generate again.
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>

        </form>

    </div>
@endsection

@push('script')
    @include('components.submitForm')
    @include('locations.components.dealsScript', ['script_type' => 'form'])
    @include('locations.components.googleaddress')

    <script>
        let typingTimer;
        let doneTypingInterval = 300;
        let lastSearchedTerm = '';
        let cachedResults = {}; // cache object
        contactId = null;

        $('#first_name').on('focus', function() {
            if (lastSearchedTerm === '') {
                fetchContacts('', true); // true = force load
            } else {
                displaySuggestions(cachedResults[lastSearchedTerm]);
            }
        });

        $('#first_name').on('keyup', function() {
            clearTimeout(typingTimer);
            let term = $(this).val();
            typingTimer = setTimeout(function() {
                if (term === lastSearchedTerm && cachedResults[term]) {
                    displaySuggestions(cachedResults[term]);
                } else {
                    fetchContacts(term, true);
                }
            }, doneTypingInterval);
        });

        // Fetch data from backend
        function fetchContacts(term, showLoader = false) {
            if (showLoader) {
                $('#contact_suggestions').html('<div class="suggestion-item">Loading...</div>').show();
            }

            $.ajax({
                url: "{{ route('coborrower.contacts.search') }}",
                method: "GET",
                data: {
                    locationId: locationId,
                    term: term
                },
                success: function(data) {
                    lastSearchedTerm = term;
                    cachedResults[term] = data; // cache
                    displaySuggestions(data);
                }
            });
        }

        let contactCache = {};
        // Show suggestions
        function displaySuggestions(data) {
            let html = '';
            if (data.length > 0) {
                data.forEach(function(item) {
                    contactCache[item.id] = item;
                    html += `<div class="suggestion-item" data-id="${item.id}">${item.name}</div>`;
                });
            } else {
                html = '<div class="suggestion-item">No Results</div>';
            }
            $('#contact_suggestions').html(html).show();
        }

        // Select item
        $(document).on('click', '.suggestion-item', function() {
            $('#first_name').val($(this).text());
            contactId = $(this).data('id');
            $('#contact_suggestions').hide();

            // let contact = contactCache[contactId];
            // if (contact) {
            //     // Fill inputs by key matching
            //     Object.keys(contact).forEach(function(key) {
            //         $(`#${key}`).val(contact[key]);
            //     });

            //     $('#contact_search').val(contact.name);
            // }

            $("#loader-overlay").css("display", "flex").hide().fadeIn();
            $.ajax({
                type: 'GET',
                data: {
                    contactId: contactId,
                    locationId: locationId,
                    type: 'both',
                },
                url: '{{ route('get.opportunities') }}',
                success: function(response) {
                    $('.oppertunitiesBox').html(response.view);
                    clearContactFields();
                    if (response.contact_fields) {
                        fillContactFields(response.contact_fields);
                    }
                    // if (response.contactView) {
                    //     $('.contactFields').html(response.contactView);
                    // }
                    $("#loader-overlay").fadeOut();
                }
            });
        });

        function fillContactFields(contact) {
            console.log(contact)
            Object.keys(contact).forEach(function(key) {
                if (key == 'drivers_licence' || key == 'insurance_card') {
                    getImageUrlFromContact(contact, key);
                } else {
                    let k = key == 'lastName' ? 'last_name' : key;
                    let $field = $('#' + k);

                    if ($field.length) {
                        $field.val(contact[key]);
                    }
                }
            });
        }

        function clearContactFields() {
            $('.contactFields').find('input, select, textarea').each(function() {
                if ($(this).attr('id') === 'first_name') return;
                $(this).val('');
            });
        }

        // Hide on outside click
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#first_name, #contact_suggestions').length) {
                $('#contact_suggestions').hide();
            }
        });

        $(document).on('click', '.create_opportunity', function() {
            $("#loader-overlay").css("display", "flex").hide().fadeIn();
            $.ajax({
                type: 'GET',
                data: {
                    contactId: contactId,
                    locationId: locationId,
                },
                url: '{{ route('create.opportunities') }}',
                success: function(response) {
                    $("#loader-overlay").fadeOut();
                }
            });
        })
    </script>
@endpush
