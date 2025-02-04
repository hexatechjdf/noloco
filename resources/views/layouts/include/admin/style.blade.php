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
    background: rgba(0, 0, 0, 0.7); /* Dark transparent background */
    z-index: 999999;
    display: none; /* Hidden by default */
    align-items: center;
    justify-content: center;
}

.loader-container {
    text-align: center;
    background: rgba(255, 255, 255, 0.1); /* Slightly transparent background */
    padding: 20px;
    border-radius: 10px;
}



</style>
