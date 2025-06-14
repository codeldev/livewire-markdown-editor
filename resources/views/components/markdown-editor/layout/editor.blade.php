@props(['key'])
<div wire:ignore>
    <textarea
        x-model="content"
        x-ref="textarea"
        id="markdown-editor-content-{{ $key }}"
        x-on:keydown="handleKeydown($event)"
        x-on:click="closeMenu()"
        x-on:blur="closeMenu()"
        x-on:input="handleInput"
        x-on:dragenter.prevent="dropFile=true"
        class="text-zinc-700 placeholder:text-zinc-500 placeholder:font-normal placeholder:text-xs w-full font-mono text-sm border border-zinc-200 rounded-lg tracking-tight leading-relaxed focus:outline-none resize-none bg-zinc-50 p-4 transition overflow-hidden min-h-[150px]"
    ></textarea>
</div>
<div
    x-ref="mirror"
    class="invisible absolute -left-[9999px] -top-[9999px] w-full z-[-1] pointer-events-none"
></div>
