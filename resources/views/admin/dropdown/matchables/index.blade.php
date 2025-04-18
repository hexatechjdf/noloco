@extends('layouts.admin')
@push('style')
    <style>
        .show_values {
            cursor: pointer;
        }
        .remove-option{
            margin-top:33px;
        }
    </style>
@endpush
@section('content')
    <div class="container ">
        <div class="row">
            <div class="col-md-12 mt-2">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="h4">Matchables Dropdowns</h4>
                        <button class="btn btn-primary add-script">Add More</button>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Table</th>
                                    <th scope="col">Column</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $item)
                                    <tr>
                                        <th>{{ $item->table }}</th>
                                        <td>{{ $item->column }}</td>
                                        <td>
                                            @include('admin.dropdown.matchables.components.action')
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

        <div class="modal fade" id="scriptModal" tabindex="-1" role="dialog" aria-labelledby="scriptModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="scriptModalLabel">Dropdown Handling</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body appendBody">

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        let optionss = `@include('admin.dropdown.matchables.components.options', ['index' => 1])`;

        $(document).ready(function() {
            $(document).on('click','.add_more_option',function() {
                $(".options-container").append(optionss);
            });

            $(document).on("click", ".remove-option", function() {
                $(this).closest(".row").remove();
            });
        });

        $(document).on('click', '.add-script', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            $.ajax({
                type: 'GET',
                url: '{{ route('admin.dropdown.matchables.get.form') }}',
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
