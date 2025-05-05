@php($cols = $index != 0 ? 'col-md-3' : 'col-md-4')
@php($option = $option ?? null)
<div class="row ">
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label"> Location Id</label>
            <input type="text" class="form-control " value="{{ @$option->key }}" name="options[keys][]" required>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label"> Title</label>
            <input type="text" class="form-control " value="{{ @$option->value }}" name="options[values][]" required>
        </div>
    </div>
    <div class="{{ $cols }}">
        <div class="mb-3">
            <label class="form-label">Type</label>
            <select class="form-control" name="options[types][]">
                <option value="automatic" {{ @$option->type == 'automatic' ? 'selected' : '' }}>Automatic</option>
                <option value="manual" {{ @$option->type == 'manual' ? 'selected' : '' }}>Manual</option>
            </select>
        </div>
    </div>

    @if ($index != 0)
        <div class="col-md-1 ">
            <button type="button" class="btn btn-danger remove-option">Remove</button>
        </div>
    @endif
</div>
