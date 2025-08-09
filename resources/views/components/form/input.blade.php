@props([
'type' => 'text', 'name', 'value' => '', 'label' => false, 'required' => false
])
@if($label)
    <label for="validationDefault{{$name}}" class="form-label">{{ $label }}</label>
@endif
<input type="{{$type}}" id="validationDefault{{$name}}" name="{{$name}}" value="{{old($name) ?? $value }}"
       @if($required) required @endif
        {{ $attributes->class(['form-control','is-invalid' => $errors->has($name)])}}
/>
<x-form.validation-feedback :name="$name" />
