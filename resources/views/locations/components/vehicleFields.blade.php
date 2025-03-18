@php($is_source = $is_source ?? true)
@php($col = $is_source ? 'col-md-6' : 'col-md-12')
<div class="row">
    <div class="{{ $col }}">
        <div class="mb-3" id="inventoriesProcessArea">
            <div class="py-2 ">
                <label>Vehicles</label>
                <select class="form-select custom_select_vehicle select2 vehicle_field form-control" name="vehicle">
                </select>
            </div>
        </div>
    </div>
    @if ($is_source)
        <div class="col-md-6">
            <div class="mb-3" id="">
                <div class="py-2 ">
                    <label>Select Source</label>
                    <select class="form-select form-control sources select2" name="source">
                        @foreach (getOptionsByModel('sources') as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
</div>
