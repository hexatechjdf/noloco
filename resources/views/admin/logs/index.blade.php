@extends('layouts.admin')
@push('style')
    <style>
    </style>
@endpush
@section('content')
    <div class="container ">
        <div class="row">
            <div class="col-md-12 mt-2">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                      <a class="nav-link {{ request()->is('admin/logs/history/deals') ? 'active' : '' }}"
                       href="{{ route('admin.logs.history','deals') }}">Deals</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link  {{request()->is('admin/logs/history/coborrower') ? 'active' : '' }}"
                       href="{{ route('admin.logs.history','coborrower') }}">Coborrower</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link {{ request()->is('admin/logs/history/customer') ? 'active' : '' }}"
                       href="{{ route('admin.logs.history','customer') }}">Customer</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link {{ request()->is('admin/logs/history/csv') ? 'active' : '' }}"
                       href="{{ route('admin.logs.history','csv') }}">CSV</a>
                    </li>
                  </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 mt-2">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="h4 text-capitalize">Logs Setting - {{$type}}</h4>
                    </div>
                    <table class="table">
                        <thead>
                          <tr>
                            <th scope="col">Table</th>
                            <th scope="col">Type</th>
                            <th scope="col">Created At</th>
                            <th scope="col">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($logs as $key => $log)
                            @php($logg = $log[0])
                            <tr>
                              <td>{{$logg->table}}</td>
                              <td>{{$logg->for}}</td>
                              <td>{{customDate($logg->created_at,'Y/m/d')}}</td>
                              <td><button class="btn btn-warning manage_mapping" data-id="{{$key}}">Manage</button></td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="mapModalLabel">Modal title</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body appendData">
          </div>

        </div>
      </div>
    </div>
@endsection

@push('script')
    @include('components.submitForm')
    <script>
        $(document).on('click','.manage_mapping',function(){
            let id = $(this).data('id');
            url = '{{route('admin.logs.history.form')}}';
            $.ajax({
                type: 'GET',
                url: url,
                data: {id:id},
                success: function(response) {
                    $('.appendData').html(response.view);
                    $('#mapModal').modal('show');
                }
            });
        })
    </script>
@endpush
