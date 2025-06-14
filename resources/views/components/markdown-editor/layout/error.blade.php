<div
    x-show="showError"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-2"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform translate-y-2"
    class="text-red-800 text-xs font-mono flex items-center gap-1.5 pr-2"
    role="alert"
    x-cloak
>
    <x-livewire-markdown-editor::markdown-editor.icons.alert class="size-4" />
    <span x-text="errorMessage"></span>
</div>
