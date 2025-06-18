@extends('layouts.admin')
@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify@latest/dist/tagify.css">
    <link rel="stylesheet" href="https://gostarauto.com/styles/700-script-190943143.css">
    <style>

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
                                        <th scope="row">{{ $item->id . $item->uuid }}</th>
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
