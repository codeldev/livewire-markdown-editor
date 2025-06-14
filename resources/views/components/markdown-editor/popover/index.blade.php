<div
    {{ $attributes }}
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    class="absolute z-[9999] bg-white border border-zinc-200 rounded-lg shadow-md w-96 left-4 mt-6"
    :style="popoverY ? `top: ${popoverY}px;` : ''"
    x-cloak
>
    <div class="absolute -top-2 left-6 size-4 rotate-45 bg-white border-t border-l border-zinc-200"></div>
    <div class="p-5 relative">
        {{ $slot }}
    </div>
    <div class="bg-zinc-100 rounded-b-lg flex justify-end gap-3 px-5 py-4">
        {{ $footer ?? '' }}
    </div>
</div>
