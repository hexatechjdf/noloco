@php($attr = $attr ?? '')
@php($readonly = $attr ? 'readonly' : '')
@php($mappingKeys = $mappingKeys ?? [])
<div class="row mb-2">
    <div class="col-md-5 ">
        <div class="form-group">
            <input type="text" class="form-control" name="attr[]" {{ $readonly }} value="{{ $attr }}">
        </div>
    </div>
    <div class="col-md-6 ">
        <div class="form-group mappingPicker">
            <input type="text" data-mapping="mappingFields" data-key="mappingkey" name="maps[{{ $attr }}]"
                value="{{ @$mappingKeys[$attr] ?? '' }}" placeholder="Select mapping values"
                class="selectedvalue nullable form-control" multiple>
        </div>
    </div>
    <div class="col-md-1 ">
        <div class="form-group remove-alert">
            <i class="bi bi-trash btn btn-danger "></i>
        </div>
    </div>
</div>
