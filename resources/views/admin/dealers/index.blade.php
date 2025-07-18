@extends('layouts.admin')
@push('style')
    <style>
        .show_values {
            cursor: pointer;
        }

        .btn-toggle {
            background-color: #ccc;
            border: none;
            padding: 6px 12px;
            border-radius: 20px;
            position: relative;
            width: 70px;
            height: 36px;
            color: #fff;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .btn-toggle .toggle-text {
            position: absolute;
            left: 34px;
            top: 7px;
            z-index: 1;
            font-size: 13px;
        }

        .btn-toggle .handle {
            position: absolute;
            top: 4px;
            left: 4px;
            width: 28px;
            height: 28px;
            background-color: white;
            border-radius: 50%;
            transition: 0.3s ease;
            z-index: 2;
        }

        .btn-toggle.active {
            background-color: #28a745 !important;
        }

        .btn-toggle.active .toggle-text {
            left: 14px;
            content: "ON";
        }

        .btn-toggle.active .handle {
            left: 38px;
        }
    </style>
@endpush
@section('content')
    <div class="container ">
        <div class="row">
            <div class="col-md-12 mt-2">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="h4">Dealers Lists</h4>
                        <button class="btn btn-primary fetch-records">Fetch Records</button>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dealers as $d)
                                    <tr>
                                        <th scope="row">{{ $d->name }}</th>
                                        <td>
                                            {{ $d->email }}
                                        </td>
                                        <td>
                                            {{ $d->phone }}
                                        </td>
                                        <td>
                                            @php($event_status = @$d->status ?? null)
                                            @php($active = @$event_status == '1' ? 'active' : '')
                                            <button type="button" data-id="{{ $d->id }}"
                                                class="btn btn-lg toggleBtn btn-toggle {{ $active }}"
                                                aria-pressed="false" autocomplete="off">
                                                <span class="toggle-text">{{ $event_status == '1' ? 'ON' : 'OFF' }}</span>
                                                <div class="handle"></div>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                        {{ $dealers->links() }}

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
            $('.toggleBtn').on('click', function() {
                $(this).toggleClass('active');
                let isActive = $(this).hasClass('active');
                const toggleText = isActive ? 'ON' : 'OFF';
                $(this).find('.toggle-text').text(toggleText);
                let id = $(this).data('id');
                $.ajax({
                    type: 'GET',
                    url: '{{ route('admin.dealers.toggle.status') }}',
                    data: {
                        id: id
                    },
                    success: function(response) {
                        toastr.success('Status Updated Successfully')
                    }
                });
            });
            $('.fetch-records').on('click', function() {
                $.ajax({
                    type: 'GET',
                    url: '{{ route('admin.dealers.fetch.records') }}',
                    success: function(response) {
                        toastr.success('Run Successfully')
                    }
                });
            });
        </script>

        @include('components.submitForm')
    @endpush
