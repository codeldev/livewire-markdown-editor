<x-livewire-markdown-editor::markdown-editor.popover
    id="youtube-popover"
    x-bind:class="{'hidden': !showPopover || activePopover !== 'youtube'}"
>
    <x-livewire-markdown-editor::markdown-editor.popover.field
        :label="trans('livewire-markdown-editor::markdown-editor.editor.popovers.youtube.label')"
        field-id="youtube-url"
        type="url"
        x-model="youtubeUrl"
        placeholder="{{ trans('livewire-markdown-editor::markdown-editor.editor.popovers.youtube.placeholder') }}"
        x-on:keydown.enter.prevent="insertYoutube()"
    />
    <x-slot:footer>
        <x-livewire-markdown-editor::markdown-editor.popover.cancel />
        <x-livewire-markdown-editor::markdown-editor.popover.submit
            x-on:click="insertYoutube()"
        />
    </x-slot:footer>
</x-livewire-markdown-editor::markdown-editor.popover>
