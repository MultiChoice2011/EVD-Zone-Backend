@props([
     'name' , 'value' => '' , 'label' => false
])

<div class="mb-3">
    @if($label)
        <label for="validationTextarea{{$name}}" class="form-label">{{$label}}</label>
    @endif
    <textarea name="{{$name}}" id="validationTextarea{{$name}}" placeholder="{{$label}}"
    {{ $attributes->class([
        'form-control',
    ])}}
    >{{old($name) ?? $value }}</textarea>
    <x-form.validation-feedback :name="$name"/>
</div>
