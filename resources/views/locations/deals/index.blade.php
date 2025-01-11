@extends('layouts.app')
@push('style')
    <link href="{{ asset('admin/assets/dashboard/css/select2.min.css') }}" rel="stylesheet">
    <style>
        .select2-container {
            width: 100% !important;
            z-index: 99999;
        }

        div.dataTables_wrapper div.dataTables_length select {
            width: 100% !important;
        }

        .show_values {
            cursor: pointer;
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
                        <div class="" id="inventoriesProcessArea">
                            <div class="py-2 ">
                                <label>Vehicles</label>
                                <select class="form-select custom_select select2 form-control" name="vehicle">
                                </select>
                            </div>
                        <button class="btn btn-primary create_deal_btn hide">Create New deal</button>
                        </div>

                        <div class="appendData">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('script')
        @include('components.submitForm')
        @include('locations.components.dealsScript')
    @endpush
