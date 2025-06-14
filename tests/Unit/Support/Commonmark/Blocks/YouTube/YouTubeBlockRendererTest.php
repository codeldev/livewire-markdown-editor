<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use Codeldev\LivewireMarkdownEditor\Support\Commonmark\Blocks\Youtube\YouTubeBlockElement;
use Codeldev\LivewireMarkdownEditor\Support\Commonmark\Blocks\Youtube\YouTubeBlockRenderer;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use Mockery as m;

describe('YouTubeBlockRenderer', function (): void
{
    it('implements NodeRendererInterface', function (): void
    {
        expect(new YouTubeBlockRenderer)
            ->toBeInstanceOf(NodeRendererInterface::class);
    });

    it('renders a YouTube embed iframe with the correct video ID', function (): void
    {
        $videoId       = 'dQw4w9WgXcQ';
        $element       = new YouTubeBlockElement($videoId);
        $childRenderer = m::mock(ChildNodeRendererInterface::class);
        $result        = (new YouTubeBlockRenderer)->render($element, $childRenderer);
        $html          = (string)$result;

        expect($result)
            ->toBeInstanceOf(Stringable::class)
            ->and($html)
            ->toContain('<iframe')
            ->toContain("src=\"https://www.youtube-nocookie.com/embed/{$videoId}\"")
            ->toContain('allowfullscreen')
            ->toContain('</iframe>');
    });

    it('returns an empty string when an exception occurs', function (): void
    {
        $paragraph     = new Paragraph();
        $childRenderer = m::mock(ChildNodeRendererInterface::class);
        $result        = (new YouTubeBlockRenderer)->render($paragraph, $childRenderer);
        $html          = (string)$result;

        expect($result)
            ->toBeInstanceOf(Stringable::class)
            ->and($html)
            ->toBe('');
    });

    it('returns a Stringable object that converts to HTML', function (): void
    {
        $videoId       = 'dQw4w9WgXcQ';
        $element       = new YouTubeBlockElement($videoId);
        $childRenderer = m::mock(ChildNodeRendererInterface::class);
        $result        = (new YouTubeBlockRenderer)->render($element, $childRenderer);
        $html          = $result->__toString();

        expect($result)
            ->toBeInstanceOf(Stringable::class)
            ->and($html)
            ->toContain('<iframe')
            ->toContain("src=\"https://www.youtube-nocookie.com/embed/{$videoId}\"");
    });

    afterEach(function (): void
    {
        m::close();
    });
});
