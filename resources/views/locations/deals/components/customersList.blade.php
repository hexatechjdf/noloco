<option value="">Select</option>
@foreach ($customers as $key => $c)
    <option value="{{ $key }}">{{ $c }}</option>
@endforeach
