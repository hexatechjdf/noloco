<div class="card-body">
    @php($tables = $tables ?? [])
    <div class="mb-3">
        <div class="form-group">
            <input type="text" class="form-control" readonly value="{{ $url->url }}">
        </div>
    </div>
    <div class="">
        <label class="form-label">Related Url's <span class="add_more_url"><i class="bi bi-plus    "></i></span></label>
        <div class="append_related_url">
            @if ($related_urls)
                @foreach ($related_urls as $keyy => $u)
                    @include('admin.mapings.custom.components.urlField', [
                        'keyy' => $loop->iteration,
                        'ru' => $u,
                    ])
                @endforeach
            @else
                @include('admin.mapings.custom.components.urlField', ['keyy' => 1])
            @endif
        </div>
    </div>
    {{-- <div class="mb-3">
        <label class="form-label">Select Table</label>
        <select class="form-control table_option select2" name="table">
            <option value="">Select Option</option>
            @foreach ($tables as $key => $table)
                @php($selected = $table_name == $table->title ? 'selected' : '')
                <option {{ $selected }} value="{{ $table->title }}">{{ $table->title }}</option>
            @endforeach
        </select>
    </div> --}}
    <label class="form-label">Mapping Fields</label>
    <div class="appendBody">
        @php($mappingKeys = json_decode($url->mapping ?? '', true) ?? [])
        <div class="">
            <div class="append_map_key">
                @foreach ($attributes as $attr)
                    @include('admin.mapings.custom.components.innerMapField')
                @endforeach
            </div>
            <div class="col-md-1 ">
                <div class="form-group add_more_map">
                    <i class="bi bi-plus btn btn-success "></i>
                </div>
            </div>
        </div>

        <div class="col-md-12 mt-2">
            <div class="form-group ">
                <label class="form-label">Searchable Fields </label>
                <select class="form-control nullable searchable_options select2" multiple name="searchable_fields[]">

                </select>
            </div>
        </div>
        <div class="col-md-12 mt-2">
            <div class="form-group ">
                <label class="form-label">Displayable Fields </label>
                <select class="form-control nullable displayable_options select2" multiple name="displayable_fields[]">

                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save changes</button>
    </div>
</div>
