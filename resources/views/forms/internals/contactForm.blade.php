@php($allowed_types = $allowed_types ?? [])
@php($form = $form ?? contactForm())
@php($heading = $heading ?? null)
@php($object = $obj ?? [])
@php($cols = $cols ?? 'col-md-12')
<div class="row {{$heading}}">
    {{-- @if($heading)
    <div class="col-md-12">
       <h2 class="form-head">{{$heading}}</h2>
       <hr>
    </div>
    @endif --}}
    @foreach($form as $key => $field)
        @php($k = @$object[$key] ??  @$object[Str::camel($key)])
        @if(in_array($field['field_type'],$allowed_types))
        @php($required = $field['is_required'] ? 'required' : '')
        @if($field['input_type'] == 'hidden')
        <input id="{{ $key }}" type="{{$field['input_type']}}"  value="{{$k}}" class="{{$key}} subkey_{{@$field['sub_key']}} form-control " {{$required}} name="{{$key}}">
        @else
            <div class="{{$cols}}">
                <label>{{ Str::title(str_replace('_', ' ', $key)) }}</label>

                @if(isset($field['is_secure']))
                <div class="mb-3 position-relative">
                    <div class="input-group">
                        <input type="text" id="{{ $key }}" class="form-control" placeholder="XXX-XX-XX34">
                        <span class="input-group-text" id="toggle-ssn">
                            <i class="bi bi-eye-slash"></i>
                        </span>
                    </div>
                </div>
                @else
                    @if($field['input_type'] == 'select')
                        <select class="{{$key}} subkey_{{@$field['sub_key']}} form-control " {{$required}} name="{{$key}}">
                            <option  value=""  >Select</option>
                             @php($options = getOptionsByModel($field['model']))
                             @foreach($options as $op)
                                @php($selected = $op == $k ? 'selected' : '')
                                <option  value="{{ $op }}"  {{ $selected }} >{{$op}}</option>
                             @endforeach
                        </select>
                    @else
                        <input id="{{ $key }}" type="{{$field['input_type']}}"  value="{{$k}}" class="{{$key}} subkey_{{@$field['sub_key']}} form-control " {{$required}} name="{{$key}}">
                    @endif
                @endif
            </div>
        @endif
        @endif
    @endforeach
</div>
