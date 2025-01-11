<form class="submitForm" action="{{ $route }}" method="POST">
    @csrf
    <div class="row mt-2">
        <div class="col-md-12">
            <div class="mb-3">
                <label class="form-label"> Path</label>
                <select class="form-control" name="path">
                    @foreach (scriptPaths() as $key => $path)
                        <option value="{{ $key }}">{{ $path }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label"> Script</label>
                <textarea class="form-control" name="script">{!! @$script->script !!}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label"> Add Executer</label>
                <input type="text" class="form-control " value="{{ @$script->executer }}" name="executer" required>
            </div>
            <div class="mb-3">
                <div class="form-check">
                    @php($checked = @$script->load_once == 1 ? 'checked' : '')
                    <input name="load_once" class="form-check-input" type="checkbox" {{ $checked }} value="1"
                        id="check">
                    <label class="form-check-label" for="check">
                        Is Load Once
                    </label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</form>
