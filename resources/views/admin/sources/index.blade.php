@extends('layouts.admin')
@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify@latest/dist/tagify.css">
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
                        <h4 class="h4">Source Lists</h4>
                        <button class="btn btn-primary add-source">Add More</button>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Title</th>
                                    <th scope="col">Created At</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sources as $source)
                                    <tr>
                                        <th scope="row">{{ $source->title }}</th>
                                        <td>
                                            {{ customDate($source->created_at, 'j M Y') }}
                                        </td>
                                        <td>
                                            @include('admin.sources.components.action')
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                        {{ $sources->links() }}

                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="sourceModal" tabindex="-1" role="dialog" aria-labelledby="scriptModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="scriptModalLabel">Source Handling</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body appendBody">

                    </div>

                </div>
            </div>
        </div>
    @endsection

    @push('script')
        <script>
            $(document).on('click', '.add-source', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                $.ajax({
                    type: 'GET',
                    url: '{{ route('admin.sources.get.form') }}',
                    data: {
                        id: id
                    },
                    success: function(response) {
                        $('.appendBody').html(response.view)
                        $('#sourceModal').modal('show');
                    }
                });

            })
        </script>

        @include('components.submitForm')
    @endpush
