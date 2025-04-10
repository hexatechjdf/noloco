<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ config('app.name', 'Laravel') }}</title>
<!-- Fonts -->
<link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">

<link href="{{ asset('plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

<link href="{{ asset('admin/assets/dashboard/css/style.css') }}" rel="stylesheet">

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

    #loader-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        /* Dark transparent background */
        z-index: 999999;
        display: none;
        /* Hidden by default */
        align-items: center;
        justify-content: center;
    }

    .loader-container {
        text-align: center;
        background: rgba(255, 255, 255, 0.1);
        /* Slightly transparent background */
        padding: 20px;
        border-radius: 10px;
    }

    .form-control {
        display: block;
        width: 100%;
        padding: .375rem .75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #212529;
        background-color: #fff !important;
        background-clip: padding-box;
        border: 1px solid #ced4da !important;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        border-radius: .375rem !important;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
    }

    label {
        color: #6c757d !important;
        /* Light gray color */
        margin-bottom: 1px;
    }

    .card-title h5 {
        font-size: 1.5rem;
        /* Adjust size as needed */
        font-weight: bold;
        /* Make it bold */
        color: #343a40;
    }

    .form-head {
        font-size: 1rem;
        /* Adjust size as needed */
        font-weight: bold;
        /* Make it bold */
        color: #343a40;
        margin-bottom: 5px;
    }

    .select2-selection.select2-selection--single {
        height: 37px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 34px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        top: 6px !important;
    }

    .select2-container--default .select2-selection--single {
        border: 1px solid #ced4da !important;
        border-radius: .375rem !important;
    }
</style>
