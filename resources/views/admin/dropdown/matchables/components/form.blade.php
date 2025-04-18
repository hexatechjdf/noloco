<form class="submitForm" action="{{ $route }}" method="POST">
    @csrf
    <div class="row mt-2">
        <div class="col-md-12">
            <div class="mb-3">
                <select class="form-control" name="table">
                    @foreach (getMappingTables() as $table)
                        @php($selected = $table->title == @$item->table ? 'selected' : '')
                        <option {{ $selected }} value="{{ $table->title }}">{{ $table->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label"> Column</label>
                <input type="text" class="form-control " value="{{ @$item->column }}" name="column" required>
            </div>
        </div>
    </div>
    <hr>
    <div class="options-container">
        @php($options = json_decode(@$item->content ?? '') ?? [])
        @if(@$item->content && count($options) > 0)
          @foreach($options as $option)
            @include('admin.dropdown.matchables.components.options',['index' => $loop->index,'option' => $option])
          @endforeach
        @else
            @include('admin.dropdown.matchables.components.options',['index' => 0])
        @endif
    </div>
    <div class="row mt-2">
        <div class="col-md-12">
            <button type="button" class="btn btn-success  add_more_option" data-dismiss="modal">Add More</button>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-md-12">
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</form>
