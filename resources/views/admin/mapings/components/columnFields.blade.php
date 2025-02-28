<input type="hidden" name="key" value="{{$keyy}}">
@php($opt = $opt ?? null)
@foreach ($columns as $col => $type)
    <div class="row mb-2">
        <div class="col-md-5">
            <input type="text" class="form-control" readonly value="{{ $col }}">
        </div>
        <div class="col-md-5">
            <div class="form-group mappingPicker">
                <input type="text" data-mapping="mappingFields" data-key="mappingkey"
                    name="mapping[{{ $col }}]" value="{{ @$mapping[$col]['column'] }}"
                    placeholder="Select mapping values"
                    class="selectedvalue nullable form-control {{$opt}}" >
            </div>
        </div>
        <div class="col-md-2">
            <input type="text"
            name="type[{{$col}}]" value="{{ $type }}"
            placeholder="" readonly
            class="form-control" >
           {{-- <select class="form-control" name="type[{{$col}}]">
             @foreach(columnsTypes() as $k=> $t)
             @php($selected = @$mapping[$col]['type'] == $k ? 'selected' : '')
             <option {{$selected}} value="{{$k}}">{{$t}}</option>
             @endforeach
           </select> --}}
        </div>
    </div>
@endforeach
