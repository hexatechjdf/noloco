@extends('layouts.admin')
@push('style')
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

        .add_more_url {
            background: #198754;
            border-radius: 50%;
            padding: 0px 3px 3px 3px;
            color: white;
            font-weight: bold;
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
                        <h4 class="h4">Manage Mapping Extention</h4>
                    </div>
                    <form method="POST" class="submitForm" action="{{ route('admin.mappings.custom.form.submit') }}">
                        @csrf
                        <input type="hidden" name="id" value="{{ $id }}">
                        @include('admin.mapings.custom.components.innerMap')
                    </form>
                </div>
            </div>
        </div>
    @endsection

    @push('script')
        @include('components.submitForm')
        @include('admin.mapings.custom.components.mappingScript')
        <script>
            let changeableAttributes = @json($attributes);
            let fieldsList = @json($data1);
            let searchable = @json($searchable);
            let displayable = @json($displayable);
            let table_name = '{{ $table_name }}';
            let custom_map_fields = `@include('admin.mapings.custom.components.innerMapField')`;
            let related_url_field = `@include('admin.mapings.custom.components.urlField')`;
            $(document).ready(function() {

                $('.select2').select2({
                    placeholder: "Select Option",
                    allowClear: true
                });

                let selectedTable = '';
                $(document).on('click', '.setMaping', function(e) {
                    e.preventDefault();
                    let id = $(this).data('id');
                    $('#mappingModal').modal('show');
                    $('.table_option').val(null);
                    selectedTable = null
                    return;
                })

                $(document).on('change', '.table_option', function() {
                    let val = $(this).val();
                    if (selectedTable && selectedTable != val) {
                        alertConfirmation(selectedTable, $(this));
                    } else {
                        selectedTable = $(this).val();
                        updateMapping(selectedTable);
                    }
                });
                // let fieldsList = makeMappedList(jsonData.data);
                initMappingPicker('.mappingPicker', {});

                $('.table_option').trigger('change');

                function processObject(obj) {
                    let searchable_values = [];
                    for (let key in obj) {
                        if (typeof obj[key] === 'string' && obj[key].includes('{{')) {
                            searchable_values.push(key);
                        }
                    }

                    setSearchableFields(searchable_values);
                }

                function setSearchableFields(searchable_values) {
                    let selectElement = $('.searchable_options');
                    let displayableElement = $('.displayable_options');
                    selectElement.empty();
                    displayableElement.empty();
                    selectElement.append('<option value="">Select Option</option>');
                    displayableElement.append('<option value="">Select Option</option>');
                    let isSelected = "";
                    searchable_values.forEach(function(value) {
                        isSelected = searchable.includes(value) && table_name === selectedTable ?
                            'selected' : '';
                        selectElement.append('<option value="' + value + '" ' + isSelected + '>' + value +
                            '</option>');

                        isSelected = displayable.includes(value) && table_name === selectedTable ?
                            'selected' : '';
                        displayableElement.append('<option value="' + value + '" ' + isSelected + '>' + value +
                            '</option>');

                    });

                    selectElement.select2();
                    displayableElement.select2();
                }

                function updateMapping(selectedTable) {
                    window.mappingFields = fieldsList[selectedTable] ?? {};
                    processObject(fieldsList[selectedTable] ?? {});
                }


                function alertConfirmation(previousValue, that) {
                    swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this! All other changes will be discarded.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, change it!',
                        cancelButtonText: 'No, cancel!',
                        reverseButtons: true
                    }).then(function(result) {
                        if (!result.value) {
                            // User clicks "No, cancel!"
                            $(that).val(previousValue).trigger('change'); // Reset to the previous value
                            selectedTable = previousValue; // Keep the previous value
                        } else {
                            // User clicks "Yes, change it!"
                            selectedTable = $(that).val(); // Update the selected value
                            updateMapping(selectedTable); // Your custom function to handle the change
                            $('.appendBody').find('.nullable').val('');
                        }
                    });
                }

                $(document).on('click', '.add_more_map', function() {
                    $('.append_map_key').append(custom_map_fields);
                    initMappingPicker('.mappingPicker', {});
                })
                $(document).on('click', '.add_more_url', function() {
                    $('.append_related_url').append(related_url_field);
                })
                $(document).on('click', '.remove-alert', function() {
                    removeAlert($(this));
                })

                function removeAlert(that) {
                    swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this! All other changes will be discarded.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, change it!',
                        cancelButtonText: 'No, cancel!',
                        reverseButtons: true
                    }).then(function(result) {
                        if (result.value) {
                            $(that).closest('.row').remove();
                        }
                    });
                }
            })
        </script>
    @endpush
