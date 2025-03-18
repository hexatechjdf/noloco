@php($item = $item ?? '')
@php($index = $index ?? 0)
@php($col = $index != 0 ? 'col-md-10' : 'col-md-12')
<div class="row s-row">
    <div class="{{ $col }}">
        <label>Title</label>
        <input type="text" name="data[]" placeholder="Key" class="form-control" value="{{ $item  }}" required>
    </div>
    {{-- <div class="{{ $col }}">
        <label>Value</label>
        <input type="text" name="data[{{ $index }}][value]" placeholder="Value" class="form-control" required>
    </div> --}}

    @if ($index != 0)
        <div class="col-md-2">
            <button type="button" class="remove-row btn btn-danger mt-4">X</button>
        </div>
    @endif
</div>
