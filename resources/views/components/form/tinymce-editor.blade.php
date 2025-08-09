@props([
'type' => 'text', 'name', 'value' => '', 'label' => false, 'required' => false
])
@if($label)
    <label for="validationDefault{{$name}}" class="form-label">{{ $label }}</label>
@endif
<textarea
    {{ $attributes->class(['form-control','is-invalid' => $errors->has($name)])}}
    id="myeditorinstance"
    name="{{$name}}"
    @if($required) required @endif
>
    {!! old($name) ?? $value !!}
</textarea>

<x-form.validation-feedback :name="$name" />
