@props(['name', 'label' => null, 'type' => 'text', 'value' => null, 'help' => null, 'required' => false])
<div {{ $attributes->only('class') }}>
    @if($label)<label for="{{ $name }}" class="form-label">{{ $label }}@if($required) <span class="text-red-500">*</span>@endif</label>@endif
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name, $value) }}" @required($required)
        {{ $attributes->except('class')->merge(['class' => 'form-input'.($errors->has($name) ? ' border-red-400' : '')]) }} />
    @if($help)<p class="form-help">{{ $help }}</p>@endif
    @error($name)<p class="form-error">{{ $message }}</p>@enderror
</div>
