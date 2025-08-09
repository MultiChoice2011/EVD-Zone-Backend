@props([
'disabled' =>  false, 'is_vendor' =>  false, 'name', 'selected' => '', 'label' => false, 'options' , 'required' => false, 'ids_model'=>false
])

@if($label)
    <label for="choices-single-{{ $name }}">{{ $label }}</label>
@endif

@if(is_array($ids_model))
    dddddddddddddddddd
@endif
<select
    name="{{ $name }}"
    data-choices
    @if($disabled)
        disabled
    @endif
    id="choices-single-{{$name}}"
    @if($required)
        required
    @endif

    {{ $attributes->class([
        'form-control',
    ]) }}
>

    @if(!$required)
        <option value=""></option>
    @endif
    @if ( $is_vendor )
        <option value="{{ auth()->user()->id }}">{{ auth()->user()->name }}</option>
    @else
        @foreach($options as $option)
            <option value="{{ $option['id'] }}"
            @if(($ids_model))
                @foreach($ids_model as $id_model)
                    @selected( $option['id'] == $id_model)
                    @endforeach
                @else
                @selected( $option['id'] == $selected)
                @endif
            > {{ $option->name}}</option>
        @endforeach
    @endif
</select>

<x-form.validation-feedback :name="$name"/>
