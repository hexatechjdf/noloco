@extends('layouts.admin')
@push('style')
    <style>
        .show_values {
            cursor: pointer;
        }
        .optionButton {
  padding: 5px;
  border: 1px solid black;
  margin: 5px;
}
    </style>
@endpush
@section('content')
    <div class="container ">
        <div class="row">
            <div class="col-md-12 mt-2">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="h4">GHL to Noloco Mapping</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="submitForm" action="{{ route('admin.mappings.ghl.form.submit') }}">
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
                                                class="selectedvalue nullable form-control options">
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
        let contact = @json($contact_fileds);
        let vehicle = @json($vehicle);
        let customer = @json($customer);
        let dealership = @json($dealership);

        // jsonDataa = convertToList(jsonDataa);
        initMappingPicker('.mappingPicker', {}, false);
    </script>
@endpush
