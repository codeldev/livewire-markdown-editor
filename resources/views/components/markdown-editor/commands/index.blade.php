<div
    x-show="showMenu"
    x-transition
    x-cloak
    class="command-menu absolute z-[9999] bg-white border border-gray-200 rounded-lg shadow-lg w-64 left-4 divide-y divide-gray-200 max-h-[367px] overflow-y-auto mt-6"
    x-bind:style="'top: ' + menuY + 'px; scrollbar-width: none; -ms-overflow-style: none;'"
    x-on:mousedown.prevent
>
    <style>
        [x-show="showMenu"]::-webkit-scrollbar
        {
            display: none;
        }
    </style>
    @foreach(config('livewire-markdown-editor.editor.commands', []) as $key => $command)
        <x-livewire-markdown-editor::markdown-editor.commands.command
            :index="$command['index']"
            :icon="$command['icon']"
            :heading='trans("livewire-markdown-editor::markdown-editor.editor.commands.{$key}.heading")'
            :subheading='trans("livewire-markdown-editor::markdown-editor.editor.commands.{$key}.subheading")'
        />
    @endforeach
</div>
