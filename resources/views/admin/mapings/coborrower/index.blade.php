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
                        <h4 class="h4">Coborrower Mapping</h4>
                        <a href="{{ route('admin.mappings.coborrower.form') }}" class="btn btn-warning">Add More</a>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Title</th>
                                    <th scope="col">Is Mapped </th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $item)
                                    <tr>
                                        <th scope="row">{{ $item->title }}</th>
                                        <th scope="row">{{ $item->mapping ? 'Yes' : 'No' }}</th>
                                        <td>
                                            <a class="btn btn-primary btn-sm m-1 "
                                                href="{{ route('admin.mappings.coborrower.form', $item->id) }}"><i
                                                    class="bi bi-pencil"></i></a>
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
    @endsection

    @push('script')
        @include('components.submitForm')
    @endpush
