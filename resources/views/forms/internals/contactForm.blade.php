@php($allowed_types = $allowed_types ?? [])
<div class="row">
    @foreach(contactForm() as $key => $field)
        @if(in_array($field['field_type'],$allowed_types))
        <div class="col-md-12">
            <label>{{ Str::title(str_replace('_', ' ', $key)) }}</label>
            @if($field['input_type'] == 'text')
               <input type="{{$field['input_type']}}" class="{{$key}} form-control" name="{{$key}}">
            @endif
        </div>
        @endif
    @endforeach
</div>
