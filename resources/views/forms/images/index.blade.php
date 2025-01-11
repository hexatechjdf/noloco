@extends('layouts.public')
@push('style')
    @include('forms.images.components.style')
@endpush
@section('content')
    <div class="">

        <!-- Uploader Section -->
        <div class="uploader d-flex justify-content-end">
            <button type="button" class="btn btn-primary saveChanging me-3 " style="display: none;">Save Change</button>
            <button id="submitImagesButton" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#imageUploaderModal">
                Add Image
            </button>
        </div>
        <!-- Image Preview Section -->
        <div class="image-preview" id="imagePreview"></div>


    </div>
    <!-- Modal Structure -->
    <div class="modal fade" id="imageUploaderModal" tabindex="-1" aria-labelledby="imageUploaderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageUploaderModalLabel">Upload Images</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="uploaderModal"></div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@push('script')
    @include('forms.images.components.script')
@endpush
