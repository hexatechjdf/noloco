<form action="{{ route('admin.logs.history.manage', $id) }}" class="submitForm" method="POST">
    @csrf

    @foreach($logs as $log)
       <div class="row mb-2">
            <div class="col-md-3">
                <div class="d-grid">
                    <label >Column:</label>
                    <input type="text" name="" class="form-control" value="{{ $log->column }}" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-grid">
                    <label >Error:</label>
                    <input type="text" class="form-control" value="{{ $log->error }}" >
                </div>
            </div>
            <div class="col-md-2">
                <div class="d-grid">
                    <label >Type:</label>
                    <select class="form-control" name="type[{{$log->column}}]">
                        @foreach(columnsTypes() as $col)
                            <option value="{{$col}}">{{$col}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="d-grid">
                    <label >Value:</label>
                    <input type="text" class="form-control" name="data[{{$log->column}}]" value="" required>
                </div>
            </div>
       </div>
    @endforeach

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
    </div>
</form>
