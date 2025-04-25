@extends('layouts.admin')
@push('style')
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <style>
        .show_values {
            cursor: pointer;
        }

        .dataTables_wrapper .dataTables_length select {
            width: 55px;
        }

        #dropdownMenuButton {
            background: none;
        }
    </style>
@endpush
@section('content')
    <div class="container ">
        <div class="row">
            <div class="col-md-12 mt-2">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h4 class="h4">Csv Outbonding</h4>
                            <div>
                                <a class="btn btn-warning" href="{{ route('admin.mappings.csv.outbound.create') }}">
                                    <i class="bi bi-plus"></i>Create Mapping
                                </a>
                                <button class="btn btn-primary run_task">
                                    <i class="bi bi-setting"></i>Run Task
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-12 mt-2">
                <div class="card">
                    <div class="card-body">
                        <table class="table" id="datatable">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Account Title</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $item)
                                    <tr>
                                        <td scope="row">{{ $loop->iteration }}</td>
                                        <td scope="row">{{ $item->title }}</td>
                                        <td scope="row">{{@$item->outboundAccount->username}}</td>
                                        <td>
                                            @include('admin.mapings.csv.outbound.action')
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $items->links() }}

                    </div>
                </div>
            </div>
        </div>
        @include('components.loader')
        @include('admin.mapings.csv.modals.ftp')
        @include('admin.mapings.csv.modals.ftpList')
        @include('admin.mapings.csv.modals.location')
    @endsection

    @push('script')
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#datatable').DataTable();
            });
        </script>
        @include('components.submitForm')
        <script>
            $(document).on('click', '.run_task', function() {
                $("#loader-overlay").css("display", "flex").hide();
                $.ajax({
                    type: 'GET',
                    url: '{{ route('csv.export.file') }}',
                    success: function(response) {
                        $("#loader-overlay").fadeOut();
                        toastr.success('Successfully');
                    }
                });
            })
        </script>
    @endpush
