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
        .swal2-container.swal2-center {
  z-index: 999999999999999 !important;
}
    </style>
@endpush
@section('content')
    <div class="container ">

        <div class="row">
            <div class="col-md-12 mt-2">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="h4">Coborrower Management - (Deal: {{$deal_id}})  <span class="cus_name"></span></h4>
                    </div>
                    <div class="card-body">
                        <div class="" id="processArea">
                            <div class="py-2 ">
                                <label>Coborrowers</label>
                                <select class="form-select custom_select contact select2 form-control" name="contact">
                                </select>
                            </div>
                        </div>

                        <div class="appendData">

                        </div>

                        @include('components.loader')
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('script')
        @include('components.submitForm')
        @include('locations.components.coborrowerScript')
    @endpush
