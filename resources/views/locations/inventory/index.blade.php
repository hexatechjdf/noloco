@extends('layouts.app')
@push('style')
<link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
<style>
     .contact-card {
            transition: transform 0.3s ease-in-out;
        }
        .contact-card:hover {
            transform: translateY(-5px);
        }
        .dataTables_wrapper .dataTables_length select {
            width: 55px;
        }
        #dropdownMenuButton {
            background:none;
        }
</style>
@endpush
@section('content')
    <div class="container-fluid ">
        <div class="row">
            <div class="col-md-12 ">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="h4">Inventory List -  <span class="inv">Vin: {{$vin}}</span></h4>
                    </div>
                    <div class="card-body">
                        <div class="appendData">

                        </div>

                        @include('components.loader')
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('script')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
           $('#userTable').DataTable(); // Initialize DataTable
        });
        let locationId = '{{$location}}';
        let vin = '{{$vin}}';
        let url = '{{route('inventory.get.list')}}'

        $(document).ready(function(){
            if(vin && location)
            {
                $("#loader-overlay").css("display", "flex").hide().fadeIn();

                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {location: locationId, vin:vin},
                    success: function(response) {
                        $("#loader-overlay").fadeOut();
                        $('.appendData').html(response.view)
                        $('#userTable').DataTable();
                    }
                });
            }else{
                toastr.error('location or vin is invalid');
            }

        })
    </script>
    {{-- $("#loader-overlay").fadeOut(); --}}
    {{-- $("#loader-overlay").css("display", "flex").hide().fadeIn(); // Ensures hidden first, then fades in --}}

    <script>
        $(document).on('click','.open_in_same_window',function(){
            let urll = $(this).attr('href');
            window.top = urll;
        })
    </script>
    @endpush
