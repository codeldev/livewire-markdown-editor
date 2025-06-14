@props(['label', 'tab'])
<button
    type="button"
    class="px-3 text-xs tracking-wider font-mono focus:outline-none transition-opacity duration-150 uppercase cursor-pointer text-zinc-800"
    x-bind:class="$wire.activeTab === '{{ $tab }}' ? 'opacity-100' : 'opacity-50'"
    wire:click="switchTab('{{ $tab }}')"
>
    {{ $label }}
</button>
