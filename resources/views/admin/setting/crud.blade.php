@extends('layouts.admin')
@push('style')
    <style>
    </style>
@endpush
@section('content')
    <div class="container ">
        <div class="row">
            <div class="col-md-8 mt-2 m-auto">
                <form class="submitForm" action="{{ route('admin.setting.crud.save', $key) }}" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <h4 class="h4">Crud Settings - {{ Str::headline($key) }}</h4>
                        </div>
                        <div class="card-body">

                            <div class=" setting-row">
                                @if (empty($data))
                                    @include('admin.setting.components.crudField')
                                @else
                                    @foreach ($data as $index => $item)
                                        @include('admin.setting.components.crudField', ['index' => $index,'item' => $item])
                                    @endforeach
                                @endif
                            </div>
                            <div class="d-flex justify-content-between mt-3">
                                <button class="btn btn-warning add_more" type="button">Add More</button>
                                <button class="btn btn-success">Submit</button>
                            </div>
                        </div>
                    </div>


                </form>
            </div>
        </div>
    @endsection

    @push('script')
        @include('components.copyUrlScript')
        @include('components.submitForm')
        <script>
            $(document).on('click', '.remove-row', function() {
                $(this).closest('.row').remove();
            });
            $(document).on('click', '.add_more', function() {
                let indexx = $('.s-row').length;
                let f = `<div class="row s-row">
    <div class="col-md-10">
        <label>Title</label>
        <input type="text" name="data[]" placeholder="Key" class="form-control" required>
    </div>

        <div class="col-md-2">
            <button type="button" class="remove-row btn btn-danger mt-4">X</button>
        </div>
</div>`;
                $('.setting-row').append(f);
            })
        </script>
    @endpush
