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
                        <h4 class="h4">Deals Management -  <span class="cus_name"></span></h4>
                    </div> --}}
                    <div class="card-body">

                        @include('locations.components.vehicleFields',['is_source' => true])
                        <div class="" id="processArea">
                            <div class="py-2 ">
                                <label>Search Existing Contacts
                                    <button class="btn btn-success btn-sm contact_createe rounded-pill" data-bs-toggle="collapse" data-bs-target="#form-box"  title="Create New">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </label>
                                <select class="form-select custom_select contact select2 form-control" name="contact">
                                </select>
                            </div>
                        </div>
                        <div class="form-box" >
                            <div class="collapse mt-2" id="form-box">
                                <div class="card card-body shadow-sm">
                                    <div class="card-title">
                                        <h5 class="">Add New Contact</h5>
                                    </div>
                                    <hr>
                                    <form id="submForm">
                                        @include('forms.internals.contactForm',['cols'=> 'col-md-4','allowed_types' => ['simple']])
                                    </form>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary create_deal_btn hide mt-3">Create New deal</button>


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

    @endpush
