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

    // Handle file processing on button click
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

        // Split headers and clean them
        const headers = rows[0]
            .split(',')
            .map(header => header.trim().replace(/^"|"$/g, '').replace(/^'|'$/g, ''));

        console.log('Cleaned Headers:', headers); // Debug log

        // Display headers with Select2 options
        let headersHtml = '<hr><h1 class="csv_head_title">Manage CSV Mapping</h1>';
        headersHtml += '<div><table class="table">';
        headersHtml += '<thead><tr><th colspan="3"><label>Enter Title</label><div><input class="form-control title" value="'+title+'" name="title"></div></th></tr><tr><th>CSV Header</th><th>Mapping Option</th><th>Select Unique Field</th></tr></thead><tbody>';

        headers.forEach(header => {
            let options = '<option value="">Select Mapping</option>';
            for (const [field, type] of Object.entries(fields)) {
                const selected = mapping.hasOwnProperty(header) && mapping[header] === `${field}__${type}` ? 'selected' : '';
                options += `<option ${selected} value="${field}__${type}">${field}</option>`;
            }
            const checked = uniqueF === header ? 'checked' : '';
            headersHtml += `
                <tr>
                    <td>${header}</td>
                    <td>
                        <select class="form-control select2 w-100" name="mapping[${header}]">
                            ${options}
                        </select>
                    </td>
                    <td>
                        <input type="radio" ${checked} name="unique_field" value="${header}" />
                    </td>
                </tr>
            `;
        });

        headersHtml += '</tbody></table></div>';
        headersHtml += '<div class="w-100"><button class="btn btn-success">Submit</button></div>';
        $('#headersContainer').html(headersHtml);

        // Initialize Select2
        $('.select2').select2();
    };

    reader.readAsText(fileInput);
});


});

    </script>
@endpush
