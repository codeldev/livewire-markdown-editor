<div
    class="px-4 py-3 border-t border-zinc-200 font-mono text-xs font-normal text-zinc-500 flex items-center"
    x-show="$wire.activeTab === 'write'"
>
    <div class="flex-1">
        <span>{{ trans('livewire-markdown-editor::markdown-editor.editor.general.command') }}</span>
    </div>
    <div class="flex items-center space-x-4 font-normal">
        <span x-text="charCount === 1
            ? '1 {{ trans('livewire-markdown-editor::markdown-editor.editor.statusbar.characters.singular') }}'
            : charCount + ' {{ trans('livewire-markdown-editor::markdown-editor.editor.statusbar.characters.plural') }}'
        "></span>
        <span x-text="wordCount === 1
            ? '1 {{ trans('livewire-markdown-editor::markdown-editor.editor.statusbar.words.singular') }}'
            : wordCount + ' {{ trans('livewire-markdown-editor::markdown-editor.editor.statusbar.words.plural') }}'
        "></span>
    </div>
</div>
