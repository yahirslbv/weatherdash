@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-bold text-sm text-slate-200']) }}>
    {{ $value ?? $slot }}
</label>
