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
                        <h4 class="h4">Csv Mapping</h4>
                        <a class="btn btn-warning" href="{{route('admin.mappings.csv.create')}}">Add</a>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Unique Field</th>
                                    <th scope="col">Total Accounts</th>
                                    <th scope="col">Total Locations</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $item)
                                    <tr>
                                        <td scope="row">{{ $loop->iteration }}</td>
                                        <td scope="row">{{ $item->title }}</td>
                                        <td scope="row">{{ $item->unique_field }}</td>
                                        <td scope="row">{{ $item->accounts_count }}</td>
                                        <td scope="row">{{ $item->locations_count }}</td>
                                        <td>
                                          @include('admin.mapings.csv.action')
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

        @include('admin.mapings.csv.modals.ftp')
        @include('admin.mapings.csv.modals.ftpList')
        @include('admin.mapings.csv.modals.location')
    @endsection

    @push('script')
        @include('components.submitForm')
        {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.4.2/zxcvbn.js"></script> --}}
        <script>
            $(document).on('click','.manage_accounts',function(){
                  let csvId = $(this).data('id');
                  $('.csv_id').val(csvId);

                  $.ajax({
                    type: 'GET',
                    url: '{{route('admin.mappings.csv.ftp.form')}}',
                    data: {csv_id : csvId},
                    success: function(response) {
                        $('.append-data').html(response.html)
                        $('.csv_id').val(csvId);
                        $('#ftpModal').modal('show')
                    }
                });

            })

            $(document).on('click','.add_account',function(){

                  $(this).closest('.modal').modal('hide')
                  let id = $(this).data('id');
                  let csvId = $(this).data('csvid');
                  let username = $(this).data('username');
                  let location = $(this).data('location');
                  $('.username').val(username);
                  $('.location').val(location);
                  $('.csv_id').val(csvId);
                  $('.id').val(id);
                  if(id)
                  {
                    $('.password_area').addClass('hidden');
                  }else{
                    $('.password_area').removeClass('hidden');
                  }
                  $('#ftpModal1').modal('show')
            })

            $(document).on('click','.remove_ftp',function(){
                let id = $(this).data('id');
                let that = $(this);
                swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then(function(result) {
                    $.ajax({
                        type: 'GET',
                        url: '{{route('admin.mappings.csv.ftp.delete')}}',
                        data: {id : id},
                        success: function(response) {
                            $(that).closest('tr').remove();
                        }
                    });
                })
            })


            $(document).on('click','.set_location',function(){
                  let csvId = $(this).data('id');
                  $.ajax({
            type: 'GET',
            url: '{{route('admin.mappings.csv.location.form')}}',
            data: {csv_id : csvId},
            success: function(response) {
                $('.append_body').html(response.html)
                $('.csv_id').val(csvId);
                $('#locationModal').modal('show')
            }
        });

            })


//             document.getElementById('password').addEventListener('input', function () {
//     const password = this.value;
//     const strength = zxcvbn(password).score; // Score ranges from 0 to 4
//     const strengthText = document.getElementById('password-strength');

//     if (strength < 3) { // Less than 3 means weak password
//         strengthText.textContent = 'Password strength: Weak (Make it stronger)';
//         strengthText.class = 'danger_field';
//     } else {
//         strengthText.textContent = 'Password strength: Strong';
//         strengthText.class = 'success_field';
//     }
// });
        </script>
    @endpush
