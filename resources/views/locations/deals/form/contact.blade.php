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
    </style>
@endpush
@section('content')
    <div class="container ">
        <div class="row">
            <div class="col-md-8  mt-2">
                @include('locations.components.vehicleFields',['is_source' => true])
                <div class="appendData mt-3">
                    <div class="form-boxx">

                        <form id="submForm" class="mt-3">
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
                                        'allowed_types' => ['simple', 'extra', 'vs'],
                                        'form' => contact_informationForm(),
                                        'obj' => $contact,
                                    ])

                                    <div class="card-title mt-3">
                                        <h5 class="">Identification</h5>
                                    </div>
                                    @include('forms.internals.fieldsSetting', [
                                        'heading' => 'Contact',
                                        'cols' => 'col-md-4',
                                        'allowed_types' => ['simple', 'extra', 'vs'],
                                        'form' => identificationForm(),
                                        'obj' => $contact,
                                    ])

                                    <div class="accordion" id="creditAppAccordion">
                                        <div class="accordion-item mt-3">
                                            <h2 class="accordion-header" id="headingCreditApp">
                                                <button class="accordion-button collapsed collapse-head" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapseCreditApp"
                                                    aria-expanded="false" aria-controls="collapseCreditApp">
                                                    <i class="bi bi-caret-right-fill me-2"></i>
                                                    Credit Application
                                                </button>
                                            </h2>
                                            <div id="collapseCreditApp" class="accordion-collapse collapse"
                                                aria-labelledby="headingCreditApp" data-bs-parent="#creditAppAccordion">
                                                <div class="accordion-body">
                                                    @include('forms.internals.fieldsSetting', [
                                                        'heading' => 'Credit App',
                                                        'cols' => 'col-md-4',
                                                        'allowed_types' => ['simple'],
                                                        'form' => creditApplicationForm(),
                                                        'obj' => $contact,
                                                    ])
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item mt-3">
                                            <h2 class="accordion-header" id="headingInsuranceApp">
                                                <button class="accordion-button collapsed collapse-head" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#collapseInsuranceApp"
                                                    aria-expanded="false" aria-controls="collapseCreditApp">
                                                    <i class="bi bi-caret-right-fill me-2"></i>
                                                    Insurance Infromation
                                                </button>
                                            </h2>
                                            <div id="collapseInsuranceApp" class="accordion-collapse collapse"
                                                aria-labelledby="headingInsuranceApp"
                                                data-bs-parent="#insuranceAppAccordion">
                                                <div class="accordion-body">
                                                    @include('forms.internals.fieldsSetting', [
                                                        'heading' => 'Trade',
                                                        'cols' => 'col-md-4',
                                                        'allowed_types' => ['simple'],
                                                        'form' => insuranceInformationForm(),
                                                        'obj' => $contact,
                                                    ])
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary mt-3 contact_field_form" type="button">Submit</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
                @include('components.loader')
            </div>
            <div class="col-md-4">

            </div>
        </div>
    </div>
@endsection

@push('script')
    @include('components.submitForm')
    @include('locations.components.dealsScript', ['script_type' => 'form'])
    @include('locations.components.googleaddress')
    <script>
        $(document).ready(function() {
            @if ($vin)
                $.ajax({
                    type: 'GET',
                    data: {
                        contactId: contactId,
                        locationId: locationId,
                    },
                    url: '{{ route('deals.inventories.search') }}?vin={{ $vin }}',
                    success: function(response) {
                        let item = null;
                        try {
                            item = Object.values(response)[0];
                        } catch (error) {
                            item = null;
                        }
                        vehiclesData[item.id] = item;

                        var option = new Option(item.name, item.id, true, true);
                        $(".vehicle_field").append(option).trigger('change');

                    }
                });
            @endif
        })

        $(document).ready(function() {
            function toggleDiv() {
                if ($("#flexCheckDefault").is(":checked")) {
                    $(".credit-fields").hide();
                } else {
                    $(".credit-fields").show();
                }
            }
            toggleDiv();
            $("#flexCheckDefault").on("change", function() {
                toggleDiv();
            });
        });
    </script>
@endpush
