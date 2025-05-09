@extends('layouts.admin')

@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dropify/dist/css/dropify.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <style>
        .show_values {
            cursor: pointer;
        }
        .select2-container {
  width: 100% !important;
}
.csv_head_title{
    font-size: 20px;
  font-weight: bold;
  text-align: center;
  margin-top: 20px;
}
    </style>
@endpush

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-2">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="h4">Upload CSV File</h4>
                        {{-- <a class="btn btn-warning" target="_blank" href="{{asset('csvsample.csv')}}"><i class="bi bi-download"></i> Sample File</a> --}}
                    </div>
                    <div class="card-body">
                        <form id="csvUploadForm" enctype="multipart/form-data">
                            <div class="form-group">
                                <input type="file" id="csvFile" class="dropify" data-allowed-file-extensions="csv" />
                            </div>
                            <button type="button" id="processCsv" class="btn btn-primary mt-3">Process CSV</button>
                        </form>
                        <form method="POST" class="submitForm" action="{{ route('admin.mappings.csv.store',$id) }}">
                            @csrf
                        <div id="headersContainer" class="mt-4">



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
    <script src="https://cdn.jsdelivr.net/npm/dropify/dist/js/dropify.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>


    <script>

$(document).ready(function () {
    // Initialize Dropify
    const fields = @json($fields);
    const mapping = @json($mapping);
    const uniqueF = '{{$unique_field}}';
    const title = '{{$title}}';
    $('.dropify').dropify();

    function initializeTable(existingHeaders = []) {
        let headersHtml = '<hr><h1 class="csv_head_title">Manage CSV Mapping</h1>';
        headersHtml += '<div><table class="table" id="csvTable">';
        headersHtml += '<thead><tr><th colspan="4"><label>Enter Title</label><div><input class="form-control title" value="'+title+'" name="title"></div></th></tr>';
        headersHtml += '<tr><th>CSV Header</th><th>Mapping Option</th><th>Select Unique Field</th><th>Action</th></tr></thead><tbody>';

        // If existing mappings are available (Edit Mode)
        if (existingHeaders.length > 0) {
            existingHeaders.forEach(header => {
                headersHtml += generateRow(header, mapping, uniqueF);
            });
        }

        headersHtml += '</tbody></table></div>';
        headersHtml += '<div class="w-100"><button id="addMore" type="button" class="btn btn-primary">Add More</button> ';
        headersHtml += '<button class="btn btn-success">Submit</button></div>';

        $('#headersContainer').html(headersHtml);
        $('.select2').select2(); // Initialize Select2 for dropdowns
    }

    // Handle CSV processing
    $('#processCsv').on('click', function () {
        const fileInput = $('#csvFile')[0].files[0];

        if (!fileInput) {
            alert('Please select a CSV file first.');
            return;
        }

        const reader = new FileReader();
        reader.onload = function (event) {
            const csvContent = event.target.result;
            const rows = csvContent.split('\n');

            // Extract and clean headers
            const headers = rows[0]
                .split(',')
                .map(header => header.trim().replace(/^"|"$/g, '').replace(/^'|'$/g, ''));

            console.log('Cleaned Headers:', headers); // Debugging

            initializeTable(headers);
        };

        reader.readAsText(fileInput);
    });

    // Function to generate table row
    function generateRow(header = '', mapping = {}, uniqueF = '') {
        let options = '<option value="">Select Mapping</option>';
        for (const [field, type] of Object.entries(fields)) {
            const selected = mapping.hasOwnProperty(header) && mapping[header]['column'] === `${field}__${type}` ? 'selected' : '';
            options += `<option ${selected} value="${field}__${type}">${field}</option>`;
        }
        const checked = uniqueF === header ? 'checked' : '';
        return `
            <tr>
                <td><input type="text" class="form-control" name="headerss[]" value="${header}"></td>
                <td>
                    <select class="form-control select2 w-100" name="mapping[${header}]">
                        ${options}
                    </select>
                </td>
                <td>
                    <input type="radio" ${checked} name="unique_field" value="${header}" />
                </td>
                <td>
                    <button class="btn btn-danger removeRow">Remove</button>
                </td>
            </tr>
        `;
    }

    // Load existing mapping on edit page
    if (Object.keys(mapping).length > 0) {
        initializeTable(Object.keys(mapping));
    }

    // Add More Button
    $(document).on('click', '#addMore', function () {
        $('#csvTable tbody').append(generateRow());
        $('.select2').select2(); // Reinitialize Select2 for new rows
    });

    // Remove Row
    $(document).on('click', '.removeRow', function () {
        $(this).closest('tr').remove();
    });
});



    </script>
@endpush
