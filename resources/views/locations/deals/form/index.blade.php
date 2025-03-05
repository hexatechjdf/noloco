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
                        <h4 class="h4">Deals Management -  <span class="cus_name"></span></h4>
                    </div>
                    <div class="card-body">
                        <div class="" id="processArea">
                            <div class="py-2 ">
                                <label>Contacts
                                    <button class="btn btn-success btn-sm contact_create rounded-pill"  title="Create New">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </label>
                                <select class="form-select custom_select contact select2 form-control" name="contact">
                                </select>
                            </div>
                        </div>
                        <div class="" id="inventoriesProcessArea">
                            <div class="py-2 ">
                                <label>Vehicles</label>
                                <select class="form-select custom_select_vehicle select2 vehicle_field form-control" name="vehicle">
                                </select>
                            </div>
                        <button class="btn btn-primary create_deal_btn hide">Create New deal</button>
                        </div>

                        <div class="appendData">

                        </div>

                        @include('components.loader')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasForm">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Add New</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <!-- Form Inside Sidebar -->
            <form>
                 @include('forms.internals.contactform',['allowed_types' => ['simple']])
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
@endsection

    @push('script')
        @include('components.submitForm')
        @include('locations.components.dealsScript',['script_type' => 'form'])
    @endpush
