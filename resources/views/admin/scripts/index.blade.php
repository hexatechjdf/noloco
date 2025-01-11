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
                        <h4 class="h4">Scripts</h4>
                        <button class="btn btn-primary add-script">Add More</button>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Path</th>
                                    <th scope="col">Executer</th>
                                    <th scope="col">Loading Type</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($scripts as $script)
                                    <tr>
                                        <th scope="row">{{ $script->path }}</th>
                                        <td>{{ $script->path }}</td>
                                        <td>{{ $script->load_once ? 'Once' : 'Repeating' }}</td>
                                        <td>
                                            @include('admin.scripts.components.action')
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                        {{ $scripts->links() }}

                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="scriptModal" tabindex="-1" role="dialog" aria-labelledby="scriptModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="scriptModalLabel">Script Handling</h5>
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
            $(document).on('click', '.add-script', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                $.ajax({
                    type: 'GET',
                    url: '{{ route('admin.scripts.get.form') }}',
                    data: {
                        id: id
                    },
                    success: function(response) {
                        $('.appendBody').html(response.view)
                        $('#scriptModal').modal('show');
                    }
                });

            })
        </script>

        @include('components.submitForm')
    @endpush
