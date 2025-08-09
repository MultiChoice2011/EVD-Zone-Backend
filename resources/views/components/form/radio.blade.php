@props([
'name', 'options', 'checked' => false, 'label' => false,'required' => false,
])

@if($label)
    <label for="">{{ $label }}</label>
@endif

@foreach($options as $value => $text)

    <div class="form-check">
        <label class="form-check-label" for=="validationFormCheck{{ $value }}">
            {{ $text }}
        </label>
        <input class="form-check-input"  id="validationFormCheck{{ $value }}" type="radio" name="{{ $name }}" value="{{ $value }}"
               @if($required) required @endif
               @checked(old($name, $checked) == $value)
        >
    </div>
    <x-form.validation-feedback :name="$name" />


@endforeach
