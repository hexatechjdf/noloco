@extends('layouts.admin')
@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify@latest/dist/tagify.css">
    <style>
        .show_values {
            cursor: pointer;
        }

        .mappingPicker :is(input#searchBar, input#searchBar:focus) {
            border: none;
            box-shadow: unset;
        }

        .mappingPicker li.list-group-item {
            display: flex;
            cursor: pointer;
            justify-content: space-between;
        }

        .mappingPicker .allList {
            margin: unset;
            padding: unset;
        }

        .mappingPicker .jsonList {
            width: 100%;
            border-radius: unset;
        }

        .mappingPicker.hidden>ul {
            display: none;
        }

        .mappingPicker {
            position: relative;
        }

        .mappingPicker ul.list-group.listPicker.absolute {
            position: absolute;
            z-index: 99999999;
        }

        .mappingPicker li.list-group-item .close {
            text-align: center;
            width: 100%;
        }
    </style>
@endpush
@section('content')
    <div class="container ">
        <div class="row">
            <div class="col-md-8 mt-2">
                <form class="submitForm" action="{{ route('admin.setting.save') }}" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <h4 class="h4">CRM OAuth Information</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <h6>Redirect URI - add while creating app</h6>
                                    <p class="h6"> {{ route('crm.oauth_callback') }} </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h6>Scopes - select while creating app</h6>
                                    <p class="h6"> {{ $scopes }} </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h6>* Note - App distribution for agency and subaccount both !</h6>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="clientID" class="form-label"> Client ID</label>
                                        <input type="text" class="form-control "
                                            value="{{ $settings['crm_client_id'] ?? '' }}" id="crm_client_id"
                                            name="setting[crm_client_id]" aria-describedby="clientID" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label for="clientID" class="form-label"> Client secret</label>
                                    <input type="text" class="form-control "
                                        value="{{ $settings['crm_client_secret'] ?? '' }}" id="crm_secret_id"
                                        name="setting[crm_client_secret]" aria-describedby="secretID" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 m-2">
                                    <button id="form_submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="h4">CRM OAuth Connectivity</h4>
                    </div>
                    <div class="card-body">
                        <div class="ml-2">
                            <p class="mb-1 text-muted">Connectivity</p>
                            @if ($company_name && $company_id)
                                <p>company : <span style="font-weight:bold;">{{ $company_name }}</span></p>
                                <p>companyId : <span style="font-weight:bold;">{{ $company_id }}</span></p>
                            @endif
                            @php($connect = @$company_name ? 'Reconnect' : 'Connect')
                            <p style="font-weight:bold; font-size:22px"><a class="btn btn-primary"
                                    href="{{ $connecturl }}">{{ $connect }} with
                                    Agency</a></p>
                        </div>
                    </div>
                </div>
                <div class="card mt-2">
                    <div class="card-header">
                        <h4 class="h4">CRM Location Setting</h4>
                    </div>
                    <div class="card-body">
                        <form class="submitForm" action="{{ route('admin.setting.save') }}" method="POST">
                            @csrf
                            <div class="ml-2">
                                <label for="clientID" class="form-label"> Location Id</label>
                                <input type="text" class="form-control "
                                    value="{{ $settings['crm_location_id'] ?? '' }}" id="crm_secret_id"
                                    name="setting[crm_location_id]" required>
                            </div>
                            <div class="row">
                                <div class="col-md-12 m-2">
                                    <button id="form_submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mt-2">
                <form class="submitForm" action="{{ route('admin.setting.save') }}" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <h4 class="h4">{{ __('file.noloco') }} Auth Setting</h4>
                        </div>
                        <div class="card-body">
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('file.noloco') }} App Name</label>
                                        <input type="text" class="form-control "
                                            value="{{ $settings['noloco_app_name'] ?? '' }}"
                                            name="setting[noloco_app_name]"required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('file.noloco') }} App Key</label>
                                        <input type="text" class="form-control "
                                            value="{{ $settings['noloco_app_key'] ?? '' }}"
                                            name="setting[noloco_app_key]"required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 m-2">
                                    <button id="form_submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-md-12 mt-2">
                <form class="submitForm" action="{{ route('admin.setting.save') }}" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <h4 class="h4">{{ __('file.noloco') }} Side Setting</h4>
                        </div>
                        <div class="card-body">
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Deals Management Link</label>
                                        <input type="text" class="form-control " readonly
                                            value="{{ route('deals.setting') }}?locationId={{ braceParser('[[location.id]]') }}&contactId={{ braceParser('[[user.contact_id]]') }}"
                                            name="">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Coborrower Management Link</label>
                                        <input type="text" class="form-control " readonly
                                            value="{{ route('coborrower.setting') }}?locationId={{ braceParser('[[location.id]]') }}&dealId={{ braceParser('[[deal.id]]') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-md-12 mt-2">
                <form class="submitForm" action="{{ route('admin.setting.save') }}" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <h4 class="h4">{{ __('file.noloco') }} Inventory Setting</h4>
                        </div>
                        <div class="card-body">
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Get Financed Path</label>
                                        <input type="text" class="form-control "
                                            value="{{ $settings['inv_financed_path'] ?? '' }}"
                                            name="setting[inv_financed_path]" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Get Inquire Path <code>(If you want open as
                                            Modal them remain empty this field)</code></label>
                                    <input type="text" class="form-control "
                                        value="{{ $settings['inv_inquire_path'] ?? '' }}"
                                        name="setting[inv_inquire_path]">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Get Inventory List Page Path <code></code></label>
                                    <input type="text" class="form-control "
                                        value="{{ $settings['inv_list_path'] ?? '' }}" name="setting[inv_list_path]">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Get Inventory Detail Page Path <code></code></label>
                                    <input type="text" class="form-control "
                                        value="{{ $settings['inv_detail_path'] ?? '' }}" name="setting[inv_detail_path]">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Inventory Key Information <code></code></label>
                                    <input type="text" class="form-control "
                                        value="{{ $settings['inv_key_info'] ?? '' }}" name="setting[inv_key_info]">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Inventory Key mapping <code></code></label>
                                    <div class="form-group mappingPicker">
                                        <input type="text" data-mapping="jsonData" data-key="mappingkey"
                                            name="setting[inv_key_mapping]" value="{{ $settings['inv_key_mapping'] }}"
                                            placeholder="Select mapping values"
                                            class="selectedvalue nullable form-control" required multiple>
                                    </div>
                                    {{-- @php($keyss = json_decode(supersetting('inv_key_mapping') ?? '', true) ?? [])
                                    <select class="form-control select2" name="setting[inv_key_mapping][]" multiple>
                                        @foreach (fields() as $f)
                                            @if (!is_array($f))
                                                @php($selected = in_array($f, $keyss) ? 'selected' : '')
                                                <option {{ $selected }} value="{{ $f }}">
                                                    {{ $f }}</option>
                                            @endif
                                        @endforeach
                                    </select> --}}
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Inventory Query Parameter <code></code></label>
                                    <input type="text" class="form-control "
                                        value="{{ $settings['inv_query_param'] ?? '' }}" name="setting[inv_query_param]">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">All Images Column Name <code>(column that
                                            exist on {{ __('file.noloco') }} CRM side)</code></label>
                                    <input type="text" class="form-control "
                                        value="{{ $settings['upl_all_image_col'] ?? '' }}"
                                        name="setting[upl_all_image_col]" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 m-2">
                                    <button id="form_submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-md-12 mt-2">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="h4">{{ __('file.noloco') }} Mapping Tables</h4>
                        <button class="btn btn-warning fetch_tables">Fetch {{ __('file.noloco') }} Tables</button>
                    </div>
                    <div class="card-body">
                        <div class="ml-2">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Title</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($noloco_tables as $table)
                                        <tr>
                                            <th scope="row">{{ $loop->iteration }}</th>
                                            <td>{{ $table }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>


        <div class="modal fade" id="tablesModal" tabindex="-1" role="dialog" aria-labelledby="tablesModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="scriptModalLabel">{{ __('file.noloco') }} Tables List</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body appendBody">

                    </div>

                </div>
            </div>
        </div>
    @endsection

    @push('script')
        @include('components.copyUrlScript')
        @include('components.submitForm')
        @include('admin.mapings.custom.components.mappingScript')
        <script>
            let jsonDataa = @json($inv_data);
            $(document).ready(function() {
                initMappingPicker('.mappingPicker', jsonDataa, true, 123);

            })
            $('.select2').select2({
                placeholder: "Select Option",
                allowClear: true
            });
            $(document).on('click', '.fetch_tables', function() {
                $.ajax({
                    type: 'GET',
                    url: '{{ route('admin.setting.fetch.nolocotables') }}',
                    success: function(response) {
                        $('.appendBody').html(response.view);
                        $('#tablesModal').modal('show');
                    }
                });
            })

            const selectAllCheckbox = $('#select-all');
            const rowCheckboxes = $('.row-checkbox');

            // Event handler for "Select All" checkbox
            $(document).on('change', '#select-all', function() {
                const isChecked = $(this).is(':checked');
                $('.row-checkbox').prop('checked', isChecked)
            });



            // Event handler for individual row checkboxes
            $(document).on('change', '.row-checkbox', function() {
                const allChecked = $('.row-checkbox').length === $('.row-checkbox').filter(':checked').length;
                $('#select-all').prop('checked', allChecked);
            });

            $(document).on('click', '.noloco_table_setting', function() {
                let selectedValues = [];
                $('.row-checkbox:checked').each(function() {
                    selectedValues.push($(this).val());
                });

                $.ajax({
                    url: '{{ route('admin.setting.fetch.nolocotables.info') }}', // Replace with your server endpoint
                    type: 'GET',
                    data: {
                        table_options: selectedValues,
                    },
                    success: function(response) {
                        try {
                            let fieldsList = makeMappedList(response.data.data);
                            console.log(fieldsList)
                            fieldsList = JSON.stringify(fieldsList);
                            submitTableFields(fieldsList)
                        } catch (e) {
                            toastr.error('There is a problem')
                        }
                    }
                });
            });


            function submitTableFields(data) {
                $.ajax({
                    url: '{{ route('admin.setting.fetch.nolocotables.submit') }}', // Replace with your server endpoint
                    type: 'POST',
                    data: {
                        data: data, // Send the data object
                        _token: '{{ csrf_token() }}' // Include CSRF token
                    },
                    success: function(response) {
                        toastr.success('Successfully Submitted ');
                        window.location.reload();

                    }
                });
            }
        </script>
    @endpush
