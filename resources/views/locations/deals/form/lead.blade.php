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
                        <h4 class="h4">Lead Form -  <span class="cus_name"></span></h4>
                    </div> --}}
                    <div class="card-body">

                        @include('locations.components.vehicleFields',['is_source' => true])
                        <div class="" id="processArea">
                            <div class="py-2 ">
                                <label>Search Existing Contacts
                                </label>
                                <select class="form-select custom_select contact select2 form-control" name="contact">
                                </select>
                            </div>
                        </div>
                        <div class="">
                            <div class="py-2 oppertunitiesBox">

                            </div>
                        </div>
                        <div class="form-boxx" >
                            <div class="card  radius-10 ">
                                <div class="card-body">
                                    <div class="card-title">
                                        <h5 class="">Personal Information</h5>
                                    </div>
                                    <hr>
                                    <form id="submForm" class="mt-3" data-tag="tag-form">
                                        <div class="Vehicle">
                                            @include('forms.internals.contactForm',['cols'=> 'col-md-4','allowed_types' => ['simple'],'form' => vehicleForm()])
                                        </div>
                                        <div class="contactFields">
                                            @include('locations.deals.components.leadContactFields')
                                        </div>

                                        <button class="btn btn-primary mt-3 contact_field_form" type="button">Submit</button>
                                    </form>
                                </div>
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
        contactId = null;
        $(document).on('change','.contact',function(){
            contactId = $(this).val();
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
                    if(response.contactView)
                    {
                        $('.contactFields').html(response.contactView);
                    }
                    $("#loader-overlay").fadeOut();
                }
            });
        })
        $(document).on('click','.create_opportunity',function(){
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
