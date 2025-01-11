<div class="row mb-2">
    @php($keyy = $keyy ?? null)
    @php($num = $keyy == 1 ? 12 : 11)
    <div class="col-md-{{ $num }} ">
        <div class="form-group">
            <input type="text" class="form-control" value="{{ @$ru }}" placeholder="Enter Url"
                name="related_urls[]" value="">
        </div>
    </div>

    @if ($num != 12)
        <div class="col-md-1 ">
            <div class="form-group remove-alert">
                <i class="bi bi-trash btn btn-danger "></i>
            </div>
        </div>
    @endif
</div>
