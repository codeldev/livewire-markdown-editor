<div wire:ignore>
    <div
        x-data="markdownEditor()"
        class="w-full bg-white rounded-xl border border-zinc-200 flex flex-col relative"
        wire:key="markdown-editor-{{ $key }}"
    >
        <x-livewire-markdown-editor::markdown-editor.layout.header
            :active-tab="$activeTab"
        />
        <div class="flex-1 flex flex-col md:flex-row min-h-0">
            <div
                class="flex-1 p-3 flex flex-col min-h-0"
                x-show="$wire.activeTab === 'write'"
                x-cloak
            >
                <div class="relative h-full flex-1 flex flex-col">
                    <x-livewire-markdown-editor::markdown-editor.layout.editor :$key />
                    <x-livewire-markdown-editor::markdown-editor.commands />
                    <x-livewire-markdown-editor::markdown-editor.commands.link />
                    <x-livewire-markdown-editor::markdown-editor.commands.youtube />
                    <x-livewire-markdown-editor::markdown-editor.layout.upload />
                </div>
            </div>
            <x-livewire-markdown-editor::markdown-editor.layout.preview
                :html="$this->previewHtml"
            />
        </div>
        <x-livewire-markdown-editor::markdown-editor.layout.status />
    </div>
    <x-livewire-markdown-editor::markdown-editor.scripts />
</div>
