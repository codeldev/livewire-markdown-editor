@props(['html'])
<div
    class="flex-1 p-6 overflow-auto min-h-0 flex flex-col"
    x-show="$wire.activeTab === 'preview'"
    x-cloak
>
    @if($html !== null)
        <div class="prose prose-sm max-w-none">
            {!! $html !!}
        </div>
    @else
        <div class="text-zinc-700 text-xs font-mono">
            {{ trans('livewire-markdown-editor::markdown-editor.editor.general.preview') }}
        </div>
    @endif
</div>
