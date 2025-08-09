@props([
'name' , 'options' ,'checked' => false,'label'=>false
])
@if($label)
    <label for="{{$name}}" class="form-label">{{ $label }}</label>
@endif
@foreach($options as $value => $text)
    <div class="form-check">
        <label class="form-check-label" for="invalidCheck{{$name}}">{{$text}}</label>
        <input type="checkbox" name="{{$name}}" value="{{$value}}"  id="invalidCheck{{$name}}"
               {{ $attributes->class([
                 'form-check-input',
                 'is-invalid' => $errors->has($name)
              ])}}
               @checked(old($name,$checked) == $value)
        >
        <x-form.validation-feedback :name="$name" />
    </div>
@endforeach
