@extends('layouts.admin')
@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify@latest/dist/tagify.css">
    <style>
        .show_values {
            cursor: pointer;
        }

        .credit-btn {
            background-color: #42526E;
            color: #fff;
            margin-right: 5px;
        }

        .custom-deal-dropdown {
            position: absolute !important;
            background: #fff;
            border: 1px solid #ddd;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
            padding: 12px;
            border-radius: 10px;
            min-width: 360px;
            max-width: 420px;
            font-family: "Arial, sans-serif";
        }

        .create-credit-btn {
            margin-top: 6px;
            width: 100%;
            font-weight: bold;
            border-radius: 6px;
            padding: 6px 0;
        }

        .credit-app-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        #creditModal {
            position: fixed;
            top: 10%;
            left: 50%;
            transform: translateX(-50%);
            background: #fff;
            padding: 25px;
            border: 1px solid #ccc;
            border-radius: 8px;
            width: 500px;
            z-index: 1000;
            font-family: sans-serif;
        }

        /* #creditModal h3 {
                                                    margin-top: 0;
                                                    margin-bottom: 15px;
                                                } */

        .form-group {
            margin-bottom: 12px;
        }

        .form-group label {
            display: block;
            margin-bottom: 4px;
            font-weight: bold;
        }

        .form-row {
            display: flex;
            gap: 10px;
        }

        .form-row .form-group {
            flex: 1;
        }

        /* #creditModal button {
                                        margin-top: 15px;
                                        margin-right: 10px;
                                        padding: 8px 14px;
                                    } */

        .form-header {
            background: #1a3a68;
            padding: 10px 0px;
            text-align: center;
            margin-bottom: 20px;
            color: white;
            border-radius: 5px;
        }

        .credit-footer-action {
            width: 100%;
            display: flex;
        }

        .credit-footer-action button {
            width: 100%;
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
                        <a class="btn btn-primary " href="{{ route('admin.scriptings.create') }}">Add More</a>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Title</th>
                                    <th scope="col">UUID </th>
                                    <th scope="col">Style Link </th>
                                    <th scope="col">Script Link </th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($scriptings as $item)
                                    <tr>
                                        <th scope="row">{{ $item->title }}</th>
                                        <th scope="row">{{ $item->uuid }}</th>
                                        <th scope="row">{{ $item->css_link }}</th>
                                        <th scope="row">{{ $item->js_link }}</th>
                                        <td>
                                            <a class="btn btn-primary btn-sm m-1 "
                                                href="{{ route('admin.scriptings.create', $item->id) }}"><i
                                                    class="bi bi-pencil"></i></a>
                                            <a class="btn btn-danger btn-sm  m-1"
                                                href="{{ route('admin.scriptings.delete', $item->id) }}"
                                                onclick="event.preventDefault(); deleteMsg('{{ route('admin.scriptings.delete', $item->id) }}')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                        {{ $scriptings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>

    </script>
@endpush
