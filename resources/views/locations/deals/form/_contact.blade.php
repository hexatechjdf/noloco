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
        .add_pill{
            padding: 2px 6px 4px 6px;
        }
    </style>
@endpush
@section('content')
    <div class="container ">
        <div class="row">
            <div class="col-md-8 m-auto mt-2">
                <div class="card">
                    {{-- <div class="card-header d-flex justify-content-between">
                        <h4 class="h4">Update Contact Form -  <span class="cus_name"></span></h4>
                    </div> --}}
                    <div class="card-body">
                       @include('locations.components.vehicleFields',['is_source' => true])
                        <hr>

                        <div class="appendData mt-3">
                            <div class="form-boxx" >

                                <form id="submForm" class="mt-3">
                                    @include('forms.internals.contactForm',['heading' => 'Vehicle','cols' => 'col-md-4','allowed_types' => ['simple'],'form' => vehicleForm()])
                                    <div class="card  radius-10 ">
                                        <div class="card-body">
                                            <div class="card-title">
                                                <h5 class="">Contact Fields</h5>
                                            </div>
                                            <hr>
                                            @include('forms.internals.contactForm',['heading' => 'Contact','cols' => 'col-md-4','allowed_types' => ['simple','extra','vs'],'form' => contactForm(),'obj' => $contact])
                                            <div class="form-check">
                                                <input class="form-check-input" checked type="checkbox" value="" id="flexCheckDefault">
                                                <label class="form-check-label" for="flexCheckDefault">
                                                  Credit fields should be hidden
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="credit-fields hidden">
                                    <div class="card   radius-10 mt-3 ">
                                        <div class="card-body">
                                            <div class="card-title">
                                                <h5 class="">Credit App Fields</h5>
                                            </div>
                                            <hr>
                                            @include('forms.internals.contactForm',['heading' => 'Credit App','cols' => 'col-md-4','allowed_types' => ['simple'],'form' => creditAppForm(),'obj' => $contact])
                                        </div>
                                    </div>
                                    <div class="card  radius-10  mt-3">
                                        <div class="card-body">
                                            <div class="card-title">
                                                <h5 class="">Trade Fields</h5>
                                            </div>
                                            <hr>
                                            @include('forms.internals.contactForm',['heading' => 'Trade','cols' => 'col-md-4','allowed_types' => ['simple'],'form' => tradeForm(),'obj' => $contact])
                                        </div>
                                    </div>
                                    </div>
                                    <button class="btn btn-primary mt-3 contact_field_form" type="button">Submit</button>
                                </form>

                            </div>
                        </div>

                        @include('components.loader')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

    @push('script')
        @include('components.submitForm')
        @include('locations.components.dealsScript',['script_type' => 'form'])
        @include('locations.components.googleaddress')
        <script>
               $(document).ready(function(){
                    @if($vin)
                        $.ajax({
                            type: 'GET',
                            data: {
                                contactId: contactId,
                                locationId: locationId,
                            },
                            url: '{{ route('deals.inventories.search') }}?vin={{$vin}}',
                            success: function(response) {
                                let item = null;
                                try{
                                    item = Object.values(response)[0];
                                }catch(error){
                                    item = null;
                                }
                               vehiclesData[item.id] = item;

                               var option = new Option(item.name, item.id, true, true);
                               $(".vehicle_field").append(option).trigger('change');

                            }
                        });
                    @endif
               })

               $(document).ready(function () {
                    function toggleDiv() {
                        if ($("#flexCheckDefault").is(":checked")) {
                            $(".credit-fields").hide();
                        } else {
                            $(".credit-fields").show();
                        }
                    }
                    toggleDiv();
                    $("#flexCheckDefault").on("change", function () {
                        toggleDiv();
                    });
            });
        </script>
    @endpush
