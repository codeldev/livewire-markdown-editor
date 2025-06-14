<x-livewire-markdown-editor::markdown-editor.popover
    id="link-popover"
    x-bind:class="{'hidden': !showPopover || activePopover !== 'link'}"
>
    <div class="space-y-4">
        <x-livewire-markdown-editor::markdown-editor.popover.field
            :label="trans('livewire-markdown-editor::markdown-editor.editor.popovers.link.text.label')"
            field-id="link-text"
            type="text"
            x-model="linkText"
            placeholder="{{ trans('livewire-markdown-editor::markdown-editor.editor.popovers.link.text.placeholder') }}"
            x-on:keydown.enter.prevent="insertLink()"
        />
        <x-livewire-markdown-editor::markdown-editor.popover.field
            label="trans('livewire-markdown-editor::markdown-editor.editor.popovers.link.url.label')"
            field-id="link-url"
            type="url"
            x-model="linkUrl"
            placeholder="{{ trans('livewire-markdown-editor::markdown-editor.editor.popovers.link.url.placeholder') }}"
            x-on:keydown.enter.prevent="insertLink()"
        />
    </div>
    <x-slot:footer>
        <x-livewire-markdown-editor::markdown-editor.popover.cancel />
        <x-livewire-markdown-editor::markdown-editor.popover.submit
            x-on:click="insertLink()"
        />
    </x-slot:footer>
</x-livewire-markdown-editor::markdown-editor.popover>
