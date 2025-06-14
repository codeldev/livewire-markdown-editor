<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use Codeldev\LivewireMarkdownEditor\Support\Commonmark\Blocks\Youtube\YouTubeBlockElement;
use Codeldev\LivewireMarkdownEditor\Support\Commonmark\Blocks\Youtube\YouTubeContinueBlockParser;
use League\CommonMark\Parser\Block\AbstractBlockContinueParser;
use League\CommonMark\Parser\Block\BlockContinueParserInterface;
use League\CommonMark\Parser\Cursor;
use Mockery as m;

describe('YouTubeContinueBlockParser', function (): void
{
    it('extends AbstractBlockContinueParser', function (): void
    {
        $parser = new YouTubeContinueBlockParser('dQw4w9WgXcQ');

        expect($parser)
            ->toBeInstanceOf(AbstractBlockContinueParser::class);
    });

    it('creates a YouTubeBlockElement with the provided video ID', function (): void
    {
        $videoId = 'dQw4w9WgXcQ';
        $parser  = new YouTubeContinueBlockParser($videoId);
        $block  = $parser->getBlock();

        expect($block)
            ->toBeInstanceOf(YouTubeBlockElement::class)
            ->and($block->getVideoId())
            ->toBe($videoId);
    });

    it('always returns BlockContinue::none() from tryContinue', function (): void
    {
        $parser            = new YouTubeContinueBlockParser('dQw4w9WgXcQ');
        $cursor            = new Cursor('Any content here');
        $activeBlockParser = m::mock(BlockContinueParserInterface::class);

        expect($parser->tryContinue($cursor, $activeBlockParser))
            ->toBeNull();
    });

    it('works with different video IDs', function (): void
    {
        $videoIds = [
            'dQw4w9WgXcQ',
            'xvFZjo5PgG0',
            'oHg5SJYRHA0',
        ];

        foreach ($videoIds as $videoId)
        {
            $block = new YouTubeContinueBlockParser($videoId)->getBlock();

            expect($block->getVideoId())
                ->toBe($videoId);
        }
    });

    afterEach(function (): void
    {
        m::close();
    });
});
