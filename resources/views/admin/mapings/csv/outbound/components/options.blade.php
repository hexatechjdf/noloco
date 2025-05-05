@php($cols = $index != 0 ? 'col-md-5' : 'col-md-6')
@php($option = $option  ?? null)
<div class="row ">
    <div class="{{ $cols }}">
        <div class="mb-3">
            <label class="form-label"> Location Id</label>
            <input type="text" class="form-control " value="{{ @$option->key }}" name="options[keys][]" required>
        </div>
    </div>
    <div class="{{ $cols }}">
        <div class="mb-3">
            <label class="form-label"> Title</label>
            <input type="text" class="form-control " value="{{ @$option->value }}" name="options[values][]" required>
        </div>
    </div>

    @if ($index != 0)
        <div class="col-md-2 ">
            <button type="button" class="btn btn-danger remove-option">Remove</button>
        </div>
    @endif
</div>
