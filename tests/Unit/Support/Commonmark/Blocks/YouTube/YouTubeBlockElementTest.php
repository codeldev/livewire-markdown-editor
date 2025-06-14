<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use Codeldev\LivewireMarkdownEditor\Support\Commonmark\Blocks\Youtube\YouTubeBlockElement;
use League\CommonMark\Node\Block\AbstractBlock;

describe('YouTubeBlockElement', function (): void
{
    it('extends AbstractBlock', function (): void
    {
        expect(new YouTubeBlockElement('dQw4w9WgXcQ'))
            ->toBeInstanceOf(AbstractBlock::class);
    });

    it('stores and returns the video ID', function (): void
    {
        $videoId = 'dQw4w9WgXcQ';

        expect(new YouTubeBlockElement($videoId)->getVideoId())
            ->toBe($videoId);
    });

    it('can be instantiated with different video IDs', function (): void
    {
        collect(['dQw4w9WgXcQ', 'xvFZjo5PgG0', 'oHg5SJYRHA0'])->each(function (string $videoId)
        {
            expect(new YouTubeBlockElement($videoId)->getVideoId())
                ->toBe($videoId);
        });
    });
});
