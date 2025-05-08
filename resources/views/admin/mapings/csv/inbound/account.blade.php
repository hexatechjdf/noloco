@extends('layouts.admin')

@push('style')
<style>
    .remove-option{
        margin-top:33px;
    }
</style>
@endpush

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-2">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="h4">FTP Account Setting</h4>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="ftpTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings"
                                    type="button" role="tab">
                                    Settings
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="create-tab" data-bs-toggle="tab"
                                    data-bs-target="#create" type="button" role="tab">
                                    Manage Account
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content mt-3">
                            <div class="tab-pane fade" id="settings" role="tabpanel">
                                <div id="ftpSettings">
                                    <ul class="list-group">
                                        <li class="list-group-item"><strong>Port:</strong> <span
                                                id="ftpUsername">{{ @$setting['ftp_port'] }}</span></li>
                                        <li class="list-group-item"><strong>IP:</strong> <span
                                                id="ftpIP">{{ @$setting['ftp_ip'] }}</span></li>
                                        {{-- <li class="list-group-item"><strong>Server:</strong> <span id="ftpServer">{{@$setting['ftp_server']}}</span></li> --}}
                                    </ul>
                                </div>
                            </div>

                            <!-- Create Account Tab -->
                            <div class="tab-pane fade show active" id="create" role="tabpanel">
                                <form method="POST" class="submitForm" action="{{ route('admin.mappings.csv.ftp') }}">
                                    @csrf
                                    <input type="hidden" class="csv_id" name="csv_id" value="{{  @$csvId }}">
                                    <input type="hidden" class="id" name="id" value="{{  @$account->id }}">
                                    @if(!@$account)
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Username</label>
                                                    <input type="text" class="form-control " name="username" value="{{ @$account->username }}"
                                                        placeholder="noloco">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3 password_area">
                                                    <label for="password" class="form-label">Password</label>
                                                    <input type="text" class="form-control" id="password" name="password"
                                                        placeholder="********">
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <hr>
                                    <div class="options-container mt-2">
                                        @php($options = json_decode(@$account->location_id ?? '') ?? [])
                                        @if(count($options) > 0)
                                          @foreach($options as $option)
                                            @include('admin.mapings.csv.inbound.components.options',['index' => $loop->index,'option' => $option])
                                          @endforeach
                                        @else
                                            @include('admin.mapings.csv.inbound.components.options',['index' => 0])
                                        @endif
                                    </div>
                                    <div class="row mt-2 mb-3">
                                        <div class="col-md-12">
                                            <button type="button" class="btn btn-success  add_more_option" data-dismiss="modal">Add More</button>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100">Set Account</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    @include('components.submitForm')

    <script>
           let optionss = `@include('admin.mapings.csv.inbound.components.options', ['index' => 1, 'option' => null])`;
           $(document).ready(function() {
            $(document).on('click','.add_more_option',function() {
                $(".options-container").append(optionss);
            });

            $(document).on("click", ".remove-option", function() {
                $(this).closest(".row").remove();
            });
        });
    </script>
@endpush
