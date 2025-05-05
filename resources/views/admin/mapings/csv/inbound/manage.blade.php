@extends('layouts.admin')
@push('style')
    <style>
        .show_values {
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
                        <h4 class="h4">Csv Mapping</h4>
                        {{-- <a class="btn btn-warning" href="{{route('admin.mappings.csv.create')}}">Add</a> --}}
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Key</th>
                                    <th scope="col">Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $key => $value)
                                    <tr>
                                        <th scope="row">{{ $key }}</th>
                                        <th scope="row">{{ $value['column'] }}</th>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mt-2">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="h4">Accounts</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Username</th>
                                    <th scope="col">Directory</th>
                                    <th scope="col">Domain</th>
                                    <th scope="col">Location Id</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($map->accounts as $item)
                                    <tr>
                                        <th scope="row">{{ $item->username }}</th>
                                        <th scope="row">{{ $item->directory }}</th>
                                        <th scope="row">{{ $item->domain }}</th>
                                        <th scope="row">{{ $item->location_id }}</th>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('script')
        <script>
        </script>
    @endpush
