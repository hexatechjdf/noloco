@extends('layouts.admin')
@push('style')
    <style>
        .show_values {
            cursor: pointer;
        }
        .optionButton {
  padding: 5px;
  border: 1px solid black;
  margin: 5px;
}
    </style>
@endpush
@section('content')
    <div class="container ">
        <div class="row">
            <div class="col-md-12 mt-2">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                      <a class="nav-link {{ request()->is('admin/setting/mapping/deals') ? 'active' : '' }}"
                       href="{{ route('admin.setting.mapping','deals') }}">Deals</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link  {{request()->is('admin/setting/mapping/coborrower') ? 'active' : '' }}"
                       href="{{ route('admin.setting.mapping','coborrower') }}">Coborrower</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link {{ request()->is('admin/setting/mapping/customer') ? 'active' : '' }}"
                       href="{{ route('admin.setting.mapping','customer') }}">Customer</a>
                    </li>
                  </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 mt-2">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="h4 text-capitalize">Mapping Setting - {{$type}}</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="submitForm" action="{{ route('admin.setting.save') }}">
                            @csrf
                            <div class="mb-3">
                                <input type="text" id="searchInput" class="form-control" placeholder="Search fields...">
                            </div>
                            <div class="row mb-2 " id="checkboxContainer">
                            @foreach ($columns as $c => $type)
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            @php($checked = array_key_exists($c,$mapping)  ? 'checked' : '')
                                            <input class="form-check-input" {{$checked}} value="{{$type}}" name="setting[{{$keyy}}][{{$c}}]" type="checkbox" value="" id="defaultCheck{{$c}}">
                                            <label class="form-check-label w-100" for="defaultCheck{{$c}}">
                                                {{ $c }}
                                            </label>
                                          </div>
                                    </div>
                            @endforeach
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
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
    <script>
        $(document).ready(function () {
    $('#searchInput').on('keyup', function () {
        var value = $(this).val().toLowerCase();
        $('#checkboxContainer .form-check-label').each(function () {
            var label = $(this).text().toLowerCase();
            if (label.includes(value)) {
                $(this).closest('.col-md-6').show(); // Show matching fields
            } else {
                $(this).closest('.col-md-6').hide(); // Hide non-matching fields
            }
        });
    });
});
    </script>
@endpush
