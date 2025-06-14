<?php

/** @noinspection StaticClosureCanBeUsedInspection */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use Codeldev\LivewireMarkdownEditor\Support\Commonmark\Blocks\Youtube\YouTubeBlockParser;
use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\MarkdownParserStateInterface;
use Mockery as m;

describe('YouTubeBlockParser', function (): void
{
    it('implements BlockStartParserInterface', function (): void
    {
        expect(new YouTubeBlockParser)
            ->toBeInstanceOf(BlockStartParserInterface::class);
    });

    it('returns BlockStart::none() for non-YouTube URLs', function (): void
    {
        $parser      = new YouTubeBlockParser();
        $cursor      = new Cursor('This is not a YouTube URL');
        $parserState = m::mock(MarkdownParserStateInterface::class);

        expect($parser->tryStart($cursor, $parserState))
            ->toBeNull();
    });

    it('recognizes youtube.com URLs and returns a BlockStart', function (): void
    {
        $parser      = new YouTubeBlockParser();
        $videoId     = 'dQw4w9WgXcQ';
        $cursor      = new Cursor("https://www.youtube.com/watch?v={$videoId}");
        $parserState = m::mock(MarkdownParserStateInterface::class);

        expect($parser->tryStart($cursor, $parserState))
            ->not->toBeNull()
            ->toBeInstanceOf(BlockStart::class)
            ->and($cursor->getPosition())
            ->toBe(mb_strlen($cursor->getLine()));
    });

    it('recognizes youtu.be URLs and returns a BlockStart', function (): void
    {
        $parser      = new YouTubeBlockParser();
        $videoId     = 'dQw4w9WgXcQ';
        $cursor      = new Cursor("https://youtu.be/{$videoId}");
        $parserState = m::mock(MarkdownParserStateInterface::class);

        expect($parser->tryStart($cursor, $parserState))
            ->not->toBeNull()
            ->toBeInstanceOf(BlockStart::class)
            ->and($cursor->getPosition())
            ->toBe(mb_strlen($cursor->getLine()));
    });

    it('handles YouTube URLs with additional parameters', function (): void
    {
        $parser      = new YouTubeBlockParser();
        $videoId     = 'dQw4w9WgXcQ';
        $cursor      = new Cursor("https://www.youtube.com/watch?v={$videoId}&t=30s");
        $parserState = m::mock(MarkdownParserStateInterface::class);

        expect($parser->tryStart($cursor, $parserState))
            ->not->toBeNull()
            ->toBeInstanceOf(BlockStart::class);
    });

    it('extracts the correct video ID from youtube.com URLs', function (): void
    {
        $videoId = 'dQw4w9WgXcQ';
        $url     = "https://www.youtube.com/watch?v={$videoId}";
        $pattern = '/^https:\/\/(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})(\S*)?$/';

        preg_match($pattern, $url, $matches);

        expect($matches)
            ->toBeArray()
            ->and($matches[3])
            ->toBe($videoId);
    });

    it('extracts the correct video ID from youtu.be URLs', function (): void
    {
        $videoId = 'dQw4w9WgXcQ';
        $url     = "https://youtu.be/{$videoId}";
        $pattern = '/^https:\/\/(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})(\S*)?$/';

        preg_match($pattern, $url, $matches);

        expect($matches)
            ->toBeArray()
            ->and($matches[3])
            ->toBe($videoId);
    });

    afterEach(function (): void
    {
        m::close();
    });
});
