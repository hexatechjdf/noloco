<form class="" action="{{ route('admin.setting.fetch.nolocotables.submit') }}" method="POST">
    @csrf
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">
                    <input type="checkbox" id="select-all" class="form-check-input">
                    <label for="select-all" class="form-check-label ms-2">All</label>
                </th>
                <th scope="col">Title</th>
            </tr>
        </thead>
        <tbody class="body_area">
            @foreach ($tables as $table)
                <tr>
                    <th scope="row">{{ $loop->iteration }}</th>
                    <th scope="row">
                        @php($checked = in_array($table, $custom_tables ?? []) ? 'checked' : '')
                        <input type="checkbox" name="table_options[]" class="form-check-input value-input row-checkbox"
                            {{ $checked }} value="{{ $table }}">
                    </th>
                    <td>{{ $table }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary close" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary noloco_table_setting">Save changes</button>
    </div>
</form>
