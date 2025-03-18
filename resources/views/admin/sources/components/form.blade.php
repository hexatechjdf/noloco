<form class="submitForm" action="{{ $route }}" method="POST">
    @csrf
    <div class="row mt-2">
        <div class="col-md-12">
            <div class="mb-3">
                <label class="form-label"> Title </label>
                <input type="text" class="form-control " value="{{ @$source->title }}" name="title" required>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</form>
