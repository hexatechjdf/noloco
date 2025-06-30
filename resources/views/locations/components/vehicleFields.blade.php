@php($is_source = $is_source ?? true)
@php($is_full = $is_full ?? null)
@php($col = !$is_source || $is_full ? 'col-md-12' : 'col-md-6')
@php($is_sold = $is_sold ?? false)
<div class="row">
    @if ($is_source)
        <div class="{{ $col }}">
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
    <div class="{{ $col }}">
        <div class="mb-3" id="inventoriesProcessArea">
            <div class="py-2 ">
                <label class="d-flex justify-content-between">Vehicles
                    @if ($is_sold)
                        <div class="form-check">
                            <input class="form-check-input sold-vehicle-condition" type="checkbox" value="" id="flexCheckDefault">
                            <label class="form-check-label" for="flexCheckDefault">
                                Only Sold Vehicles
                            </label>
                        </div>
                    @endif
                </label>
                <select class="form-select custom_select_vehicle select2 vehicle_field form-control" name="vehicle">
                </select>
            </div>
        </div>
    </div>

</div>
