@php($allowed_types = $allowed_types ?? [])
@php($form = $form ?? contactForm())
@php($heading = $heading ?? null)
@php($object = $obj ?? [])
@php($cols = $cols ?? 'col-md-12')
<div class="row {{$heading}}">
    @if($heading)
       <h2>{{$heading}}</h2>
    @endif
    @foreach($form as $key => $field)
        @if(in_array($field['field_type'],$allowed_types))
        @php($required = $field['is_required'] ? 'required' : '')
        <div class="{{$cols}}">
            <label>{{ Str::title(str_replace('_', ' ', $key)) }}</label>
            @if($field['input_type'] == 'text')
               @php($k = @$object[$key] ??  @$object[Str::camel($key)])
               <input type="{{$field['input_type']}}"  value="{{$k}}" class="{{$key}} subkey_{{@$field['sub_key']}} form-control" {{$required}} name="{{$key}}">
            @endif
        </div>
        @endif
    @endforeach
</div>
