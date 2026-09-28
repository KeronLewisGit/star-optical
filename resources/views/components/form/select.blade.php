@props(['name', 'label' => null, 'options' => [], 'value' => null, 'placeholder' => null, 'help' => null, 'required' => false])
@php $selected = old($name, $value); @endphp
<div {{ $attributes->only('class') }}>
    @if($label)<label for="{{ $name }}" class="form-label">{{ $label }}@if($required) <span class="text-red-500">*</span>@endif</label>@endif
    <select id="{{ $name }}" name="{{ $name }}" @required($required) {{ $attributes->except('class')->merge(['class' => 'form-input'.($errors->has($name) ? ' border-red-400' : '')]) }}>
        @if($placeholder)<option value="">{{ $placeholder }}</option>@endif
        @foreach($options as $optValue => $optLabel)
            @php $v = is_int($optValue) ? $optLabel : $optValue; @endphp
            <option value="{{ $v }}" @selected((string) $selected === (string) $v)>{{ $optLabel }}</option>
        @endforeach
    </select>
    @if($help)<p class="form-help">{{ $help }}</p>@endif
    @error($name)<p class="form-error">{{ $message }}</p>@enderror
</div>
