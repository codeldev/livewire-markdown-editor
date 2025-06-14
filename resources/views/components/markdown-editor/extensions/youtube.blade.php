@props(['videoId'])
<div class="w-full aspect-video overflow-hidden rounded-xl">
    <iframe
        class="w-full h-full rounded-xl overflow-hidden"
        width="560"
        height="315"
        src="https://www.youtube-nocookie.com/embed/{{ $videoId }}"
        frameborder="0"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
        referrerpolicy="strict-origin-when-cross-origin"
        allowfullscreen
    >
    </iframe>
</div>
