@extends('layouts.admin')
@push('style')
    <style>
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
                        <h4 class="h4">Customer Mapping</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="submitForm" action="{{ route('admin.mappings.customer.form.submit') }}">
                            @csrf
                            @foreach ($columns as $col)
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" readonly value="{{ $col }}">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mappingPicker">
                                            <input type="text" data-mapping="mappingFields" data-key="mappingkey"
                                                name="mapping[{{ $col }}]" value="{{ @$mapping[$col] }}"
                                                placeholder="Select mapping values"
                                                class="selectedvalue nullable form-control"  multiple>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    @include('components.submitForm')
    @include('admin.mapings.custom.components.mappingScript')

    <script>
        let jsonDataa = @json($contact_fileds);

        // jsonDataa = convertToList(jsonDataa);
        initMappingPicker('.mappingPicker', {}, false, true);
    </script>
@endpush
