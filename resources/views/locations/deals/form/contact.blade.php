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
            <div class="col-md-12 mt-2">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="h4">Update Contact Form -  <span class="cus_name"></span></h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3" id="inventoriesProcessArea">
                            <div class="py-2 ">
                                <label>Vehicles</label>
                                <select class="form-select custom_select_vehicle select2 vehicle_field form-control" name="vehicle">
                                </select>
                            </div>
                        </div>

                        <hr>

                        <div class="appendData mt-3">
                            <form id="submForm">
                                @include('forms.internals.contactform',['heading' => 'Vehicle','cols' => 'col-md-4','allowed_types' => ['simple'],'form' => vehicleForm()])
                                @include('forms.internals.contactform',['heading' => 'Contact','cols' => 'col-md-4','allowed_types' => ['simple'],'form' => contactForm(),'obj' => $contact])
                                @include('forms.internals.contactform',['heading' => 'Credit App','cols' => 'col-md-4','allowed_types' => ['simple'],'form' => creditAppForm(),'obj' => $contact])
                                @include('forms.internals.contactform',['heading' => 'Trade','cols' => 'col-md-4','allowed_types' => ['simple'],'form' => tradeForm(),'obj' => $contact])
                            </form>
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
        </script>
    @endpush
