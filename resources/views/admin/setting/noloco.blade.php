@extends('layouts.admin')
@push('style')
    <style>

    </style>
@endpush
@section('content')
    <div class="container ">
        <div class="row">

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
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Inventory Link Search Contacts</label>
                                        <input type="text" class="form-control " readonly
                                            value="{{ route('inventory.setting') }}?locationId={{ braceParser('[[location.id]]') }}&vin={{ braceParser('[[inventory.vin]]') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-12 mt-2">
                <div class="card">
                    <div class="card-header">
                        <h4 class="h4">Ghl contact webhook url for workflow</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger">
                            <ul>
                                <li>Add below url to your webhook url.</li>
                            </ul>
                        </div>
                        <div class="copy-container">
                            <input type="text" class="form-control code_url"
                                value="{{ route('ghl.to.noloco') }}"
                                readonly>
                            <div class="row my-2">
                                <div class="col-md-12" style="text-align: left !important">
                                    <button type="button" class="btn btn-primary script_code copy_url" data-message="Link Copied"
                                        id="kt_account_profile_details_submit">Copy URL</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                <form class="submitForm" action="{{ route('admin.setting.save') }}" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <h4 class="h4">{{ __('file.noloco') }} Deals Setting</h4>
                        </div>
                        <div class="card-body">
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Column for vehicle relation</label>
                                        <input type="text" class="form-control "
                                            value="{{ $settings['deal_vehicle_col'] ?? '' }}"
                                            name="setting[deal_vehicle_col]" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Column for dealership relation</label>
                                        <input type="text" class="form-control "
                                            value="{{ $settings['deal_dealership_col'] ?? '' }}"
                                            name="setting[deal_dealership_col]" required>
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
                            <h4 class="h4">FTP Setting</h4>
                        </div>
                        <div class="card-body">
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" class="form-control "
                                            value="{{ $settings['ftp_username'] ?? '' }}"
                                            name="setting[ftp_username]" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">API key</label>
                                        <input type="text" class="form-control "
                                            value="{{ $settings['ftp_api'] ?? '' }}"
                                            name="setting[ftp_api]" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Domain</label>
                                        <input type="text" class="form-control "
                                            value="{{ $settings['ftp_domain'] ?? '' }}"
                                            name="setting[ftp_domain]" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Port</label>
                                        <input type="text" class="form-control "
                                            value="{{ $settings['ftp_port'] ?? '' }}"
                                            name="setting[ftp_port]" required>
                                    </div>
                                </div>
                                {{-- <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Server</label>
                                        <input type="text" class="form-control "
                                            value="{{ $settings['ftp_server'] ?? '' }}"
                                            name="setting[ftp_server]" required>
                                    </div>
                                </div> --}}
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Host / Server / IP Address</label>
                                        <input type="text" class="form-control "
                                            value="{{ $settings['ftp_ip'] ?? '' }}"
                                            name="setting[ftp_ip]" required>
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
