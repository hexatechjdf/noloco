@extends('layouts.app')
@push('style')
    <style>

    </style>
@endpush
@section('content')
    <div class="container ">
        <div class="row">
            <div class="col-md-8 m-auto">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="h4">Credit App Setting</h4>
                    </div>
                    <div class="card-body">
                        <div class="">
                            <form  class="mt-3 submitForm" method="POST" action="{{ route('location.credit-app.setting.store') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="client_id" class="form-label">Client ID</label>
                                    <input type="text" class="form-control" value="{{ @$set->client_id }}" name="client_id"
                                        placeholder="Enter Client ID">
                                </div>

                                <div class="mb-3">
                                    <label for="client_secret" class="form-label">Client Secret</label>
                                    <input type="text" class="form-control" value="{{ @$set->client_secret }}" name="client_secret"
                                        placeholder="Enter Client Secret">
                                </div>

                                <div class="mb-3">
                                    <label for="account" class="form-label">Account</label>
                                    <input type="text" class="form-control"value="{{ @$set->account }}" name="account"
                                        placeholder="Enter Account">
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="text" class="form-control" value="{{ @$set->password }}" name="password"
                                        placeholder="Enter Password">
                                </div>
                                <button class="btn btn-primary mt-3" type="submit">Submit</button>
                            </form>
                        </div>
                        @include('components.loader')
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('script')
        @include('components.submitForm')
    @endpush
