@props(['label', 'fieldId'])
<div class="space-y-1.5">
    <label
        for="{{ $fieldId }}"
        class="block text-zinc-500 font-mono text-xs tracking-wider"
    >
        {{ $label }}
    </label>
    <input {{ $attributes->merge([
        'id'    => $fieldId,
        'class' => 'w-full px-3 py-2.5 text-zinc-700 border border-zinc-300 font-mono text-xs rounded-md outline-none focus:outline-none focus:border-blue-500'
    ]) }}
    >
</div>
