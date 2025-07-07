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
            <div class="col-md-10 mt-2">
                @php($activeData = request()->query('data'))
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link {{ !$activeData ? 'active' : '' }}"
                            href="{{ route('admin.setting.mapping', 'deals') }}">Deals</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeData === 'customer' ? 'active' : '' }}"
                            href="{{ route('admin.setting.mapping', 'deals') }}?data=customer">
                            Customer
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ $activeData === 'coborrower' ? 'active' : '' }}"
                            href="{{ route('admin.setting.mapping', 'deals') }}?data=coborrower">
                            Co-Borrower
                        </a>
                    </li>
                </ul>
            </div>
            <div class="col-md-2 mt-2">
                <button class="btn btn-warning fetch_fields">Fetch Fields</button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 mt-2">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="h4 text-capitalize">Mapping Setting </h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="submitForm" action="{{ route('admin.setting.save') }}">
                            @csrf
                            <div class="mb-3">
                                <input type="text" id="searchInput" class="form-control" placeholder="Search fields...">
                            </div>
                            <div class="row mb-2 " id="checkboxContainer">
                                @foreach ($columns as $c => $type)
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            @php($checked = array_key_exists($c, $mapping) ? 'checked' : '')
                                            <input class="form-check-input" {{ $checked }} value="{{ $type }}"
                                                name="setting[{{ $keyy }}][{{ $c }}]" type="checkbox"
                                                value="" id="defaultCheck{{ $c }}">
                                            <label class="form-check-label w-100" for="defaultCheck{{ $c }}">
                                                {{ $c }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
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
    @include('components.loader')
@endsection

@push('script')
    <script>
        let tableName = '{{ $prefix }}';
    </script>
    @include('components.submitForm')
    <script>
        $(document).ready(function() {
            $('#searchInput').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('#checkboxContainer .form-check-label').each(function() {
                    var label = $(this).text().toLowerCase();
                    if (label.includes(value)) {
                        $(this).closest('.col-md-6').show(); // Show matching fields
                    } else {
                        $(this).closest('.col-md-6').hide(); // Hide non-matching fields
                    }
                });
            });
        });
    </script>
@endpush
