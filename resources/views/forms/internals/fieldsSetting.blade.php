@php($allowed_types = $allowed_types ?? [])
@php($form = $form ?? contactForm())
@php($heading = $heading ?? null)
@php($object = $obj ?? [])
@php($cols = $cols ?? 'col-md-12')
<div class="row {{ $heading }} form-rows" >
    @foreach ($form as $key => $field)
        @php($k = $object[$key] ?? ($object[\Illuminate\Support\Str::camel($key)] ?? ''))
        @php($colSize = $field['col'] ?? (\Illuminate\Support\Str::after($cols, 'col-') ?? 'md-12'))
        @php($columnClass = 'col-' . $colSize)
        @php($label = $field['title'] ?? \Illuminate\Support\Str::title(str_replace('_', ' ', $key)))
        @php($required = $field['is_required'] ? 'required' : '')

        @if (in_array($field['field_type'], $allowed_types))
            @if ($field['input_type'] === 'hidden')
                <input id="{{ $key }}" type="hidden" value="{{ $k }}"
                    class="{{ $key }} subkey_{{ $field['sub_key'] ?? '' }} form-control"
                    name="{{ $key }}">
            @else
                <div class="{{ $columnClass }}">
                    <label for="{{ $key }}">{{ $label }}</label>

                    @if (isset($field['is_secure']))
                        <div class="mb-3 position-relative">
                            <div class="input-group">
                                <input type="text" id="{{ $key }}" class="form-control"
                                    placeholder="XXX-XX-XX34" name="{{ $key }}" value="{{ $k }}">
                                <span class="input-group-text" id="toggle-ssn">
                                    <i class="bi bi-eye-slash"></i>
                                </span>
                            </div>
                        </div>
                    @elseif($field['input_type'] === 'select')
                        <select class="{{ $key }} subkey_{{ $field['sub_key'] ?? '' }} form-control"
                            {{ $required }} name="{{ $key }}">
                            <option value="">Select</option>
                            @php($options = getOptionsByModel($field['model']))
                            @foreach ($options as $op)
                                <option value="{{ $op }}" {{ $op == $k ? 'selected' : '' }}>
                                    {{ $op }}</option>
                            @endforeach
                        </select>
                    @else
                        <input id="{{ $key }}" type="{{ $field['input_type'] }}" value="{{ $k }}"
                            class="{{ $key }} subkey_{{ $field['sub_key'] ?? '' }} form-control"
                            {{ $required }} name="{{ $key }}">
                    @endif
                </div>
            @endif
        @endif
    @endforeach
</div>
