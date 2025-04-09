<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ config('app.name', 'Laravel') }}</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600"
    rel="stylesheet">

<link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/vendors.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/bootstrap.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/bootstrap-extended.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/colors.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/components.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/themes/dark-layout.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/themes/bordered-layout.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/themes/semi-dark-layout.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/core/menu/menu-types/vertical-menu.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/style.css')}}">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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

<style>
    .fa{
        height: 1.5rem;
        width: 1.5rem;
        font-size: 1.5rem;
    }
</style>
