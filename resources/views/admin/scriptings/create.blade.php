@extends('layouts.admin')
@push('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .select2-container .select2-selection--multiple {
            min-height: 129px;
        }
    </style>
@endpush
@section('content')
    <div class="container ">
        <div class="row">
            <div class="col-md-12 mt-2">
                <div class="card">
                    <div class="card-header d-flex justify-content-between" id="toolbar-contact-buttons">
                        <h4 class="h4">Scriptings</h4>
                    </div>
                    <div class="card-body">
                        <form class="submitForm" action="{{ route('admin.scriptings.store', $id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" id="title" name="title" class="form-control"
                                    placeholder="Enter title" value="{{ @$scripting->title }}">
                            </div>

                            <!-- Location Select2 Field -->
                            <div class="mb-3" id="processArea">
                                <label for="locations" class="form-label">Select Locations</label>
                                <select id="locations" name="locations[]" class="form-select locations" multiple>
                                </select>
                            </div>

                            <!-- CSS Textarea -->
                            <div class="mb-3">
                                <label for="css" class="form-label">CSS</label>
                                <textarea id="css" rows="20" name="css" class="form-control" placeholder="Write CSS here...">{{ @$scripting->css }}</textarea>
                            </div>

                            <!-- JS Textarea -->
                            <div class="mb-3">
                                <label for="js" class="form-label">JavaScript</label>
                                <textarea id="js" rows="20" name="js" class="form-control" placeholder="Write JS here...">{{ @$scripting->js }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @include('components.submitForm')
    <script>
        $(document).ready(function() {
            var preselectedLocations = @json($selectedLocations);
            var $select = $('#locations').select2({
                placeholder: "Select locations",
                allowClear: true, // shows a clear (X) button
                minimumResultsForSearch: 0 // ensures search box always shows, even for few items
            });

            $.ajax({
                type: 'GET',
                url: "{{ route('admin.locations.search') }}",
                success: function(locations) {
                    locations.forEach(function(loc) {
                        var isSelected = preselectedLocations.includes(loc.id);
                        var option = new Option(loc.name, loc.id, isSelected, isSelected);
                        $select.append(option);
                    });
                    $select.trigger('change');
                }
            });

            $('.locationds').select2({
                placeholder: 'Select a Locations',
                allowClear: true,
                dropdownParent: $("#processArea"),
                ajax: {
                    url: "{{ route('admin.locations.search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term // Send the search term to the server
                        };
                    },
                    processResults: function(data) {
                        console.log(data);
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: item.name,
                                    id: item.id
                                };
                            })
                        };
                    },
                    cache: true
                }
            });
        });
    </script>
@endpush
