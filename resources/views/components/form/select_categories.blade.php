@props([
'name', 'selected' => '', 'label' => false, 'options'
])

@if($label)
    <label for="choices-single-{{$name}}">{{ $label }}</label>
@endif

<select
    name="{{$name}}" data-choices id="choices-single-{{$name}}"
    {{ $attributes->class([
        'form-control',
    ]) }}
>
    <option value=""></option>
    @foreach($options as $category)

        <option value="{{$category->id}}"  @selected( $category['id'] == $selected)>
            {{$category->translations[0]->name}}
        </option>
        @if(count($category->child) > 0 )
            @foreach($category->child as $child)
                <option value="{{$child->id}}" @selected( $child['id'] == $selected)>
                    {{ $category->translations[0]->name . " > ".$child->translations[0]->name}}
                </option>
                @if(count($child->child) > 0 )
                    @foreach($child->child as $child2)
                        <option value="{{$child2->id}}" @selected( $child2['id'] == $selected)>
                            {{ $category->translations[0]->name . " > ". $child->translations[0]->name . " > ".$child2->translations[0]->name}}
                        </option>
                    @endforeach
                @endif
            @endforeach

        @endif
    @endforeach
</select>


<x-form.validation-feedback :name="$name"/>
