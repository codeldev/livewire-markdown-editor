<div
    x-ref="drop"
    x-show="dropFile"
    x-on:dragleave.self="$event.preventDefault(); dropFile=false"
    x-on:dragover.prevent
    x-on:drop="$event.preventDefault(); droppingFile($event); dropFile=false"
    class="absolute inset-0 z-20 w-full h-full flex items-center justify-center p-5"
    x-cloak
>
    <div class="w-full h-full border-2 border-dashed border-zinc-300 rounded-lg flex items-center justify-center gap-1.5 text-zinc-400">
        <x-livewire-markdown-editor::markdown-editor.icons.image
            class="size-6"
        />
        <span class="font-mono text-xs">
            {{ trans('livewire-markdown-editor::markdown-editor.editor.general.image') }}
        </span>
    </div>
</div>
