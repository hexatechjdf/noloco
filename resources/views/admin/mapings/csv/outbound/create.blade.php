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

        .csv_head_title {
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
                    <div class="card-body">
                        <h4 class="h4">Csv Outbonding</h4>
                        <form method="POST" class="submitForm"
                            action="{{ route('admin.mappings.csv.outbound.store', $id) }}">
                            @csrf
                            <div id="headersContainer" class="mt-4">
                                <table class="table">
                                    <thead>
                                        <tr><th colspan="3"><label>Enter Title</label><div><input class="form-control title" value="{{ @$item->title }}" name="title"></div></th></tr>
                                        <tr>
                                            <th><label>Host</label><div><input class="form-control title" value="{{ @$item->title }}" name="host"></div></th>
                                            <th><label>Username</label><div><input class="form-control title" value="{{ @$item->title }}" name="user_name"></div></th>
                                            <th><label>Password</label><div><input class="form-control title" value="{{ @$item->title }}" name="password"></div></th>
                                        </tr>
                                        <tr><th colspan="3"><label>Enter Locations <code>Comma saperated</code></label><div><input class="form-control " value="" name="location_ids"></div></th></tr>
                                        <tr>
                                            <th scope="col">Noloco Field</th>
                                            <th scope="col">Displayable Name</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($fields as $key => $field)
                                            <tr>
                                                <td><input readonly value="{{ $key }}" class="form-control"></td>
                                                @php($vl = $id ? $field : Str::headline($key))
                                                <td><input name="maps[{{ $key }}]"
                                                        value="{{ $vl }}" class="form-control"></td>
                                                <td><button class="btn btn-danger remove_row">Remove</button></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="">
                                <button class="btn btn-success w-100">Submit</button>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $(document).on('click','.remove_row',function(){
                $(this).closest('tr').remove();
            })
        });
    </script>
@endpush
