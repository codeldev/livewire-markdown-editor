@props([
    'index',
    'icon',
    'heading',
    'subheading'
])

<button
    type="button"
    class="w-full text-left px-4 py-3 hover:bg-zinc-50 focus:outline-none flex items-center gap-3 cursor-pointer"
    x-bind:class="{ 'bg-zinc-100': {{ $index }} === selected }"
    x-on:click.prevent="selectCommand({{ $index }})"
    x-on:mouseenter="selected = {{ $index }}"
    data-index="{{ $index }}"
>
    <div class="flex-none size-9 border border-zinc-300 rounded-lg flex items-center justify-center">
        <x-dynamic-component
            component="livewire-markdown-editor::markdown-editor.icons.{{ $icon }}"
            class="size-5 text-zinc-600"
        />
    </div>
    <div class="flex-1 font-mono text-xs space-y-0.5">
        <div class="font-medium text-zinc-700">{{ $heading }}</div>
        <div class="text-zinc-400 text-[0.7rem]">{{ $subheading }}</div>
    </div>
</button>
